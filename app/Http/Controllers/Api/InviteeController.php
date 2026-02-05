<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Invitee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InviteeController extends Controller
{
    /**
     * List invitees for an assessment
     */
    public function index(Request $request, string $assessmentId): JsonResponse
    {
        $assessment = $this->getOwnedAssessment($request, $assessmentId);

        $invitees = $assessment->invitees()
            ->with('testSession:id,invitee_id,status,percentage,passed')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json($invitees);
    }

    /**
     * Add invitees (manual or CSV upload)
     */
    public function store(Request $request, string $assessmentId): JsonResponse
    {
        $assessment = $this->getOwnedAssessment($request, $assessmentId);

        if ($assessment->status === 'completed') {
            throw ValidationException::withMessages([
                'assessment' => ['Cannot add invitees to a completed assessment.'],
            ]);
        }

        $validated = $request->validate([
            'emails' => ['required_without:csv', 'array', 'max:1000'],
            'emails.*' => ['email', 'max:255'],
            'csv' => ['required_without:emails', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $emails = [];

        if (isset($validated['emails'])) {
            $emails = array_unique(array_map('strtolower', $validated['emails']));
        } elseif ($request->hasFile('csv')) {
            $emails = $this->parseCSV($request->file('csv'));
        }

        // Validate email count
        if (count($emails) > 1000) {
            throw ValidationException::withMessages([
                'emails' => ['Maximum 1000 emails per batch.'],
            ]);
        }

        // Filter out already existing emails
        $existingEmails = $assessment->invitees()
            ->whereIn(DB::raw('LOWER(email)'), $emails)
            ->pluck('email')
            ->map(fn ($e) => strtolower($e))
            ->toArray();

        $newEmails = array_diff($emails, $existingEmails);

        $created = 0;
        $skipped = count($existingEmails);

        DB::transaction(function () use ($assessment, $newEmails, &$created) {
            foreach ($newEmails as $email) {
                Invitee::create([
                    'assessment_id' => $assessment->id,
                    'email' => $email,
                    'invite_token' => Str::random(64),
                    'status' => 'pending',
                ]);
                $created++;
            }

            $assessment->update(['total_invites' => $assessment->invitees()->count()]);
        });

        return response()->json([
            'message' => 'Invitees added successfully.',
            'created' => $created,
            'skipped' => $skipped,
            'total' => $assessment->fresh()->total_invites,
        ], 201);
    }

    /**
     * Remove invitee
     */
    public function destroy(Request $request, string $assessmentId, string $inviteeId): JsonResponse
    {
        $assessment = $this->getOwnedAssessment($request, $assessmentId);

        $invitee = $assessment->invitees()
            ->where('id', $inviteeId)
            ->firstOrFail();

        if ($invitee->hasStarted()) {
            throw ValidationException::withMessages([
                'invitee' => ['Cannot remove invitee who has already started the test.'],
            ]);
        }

        $invitee->delete();
        $assessment->update(['total_invites' => $assessment->invitees()->count()]);

        return response()->json([
            'message' => 'Invitee removed successfully.',
        ]);
    }

    /**
     * Send email invitations
     */
    public function sendInvites(Request $request, string $assessmentId): JsonResponse
    {
        $assessment = $this->getOwnedAssessment($request, $assessmentId);

        if (!in_array($assessment->status, ['scheduled', 'active'])) {
            throw ValidationException::withMessages([
                'assessment' => ['Assessment must be published before sending invites.'],
            ]);
        }

        $validated = $request->validate([
            'invitee_ids' => ['nullable', 'array'],
            'invitee_ids.*' => ['uuid', 'exists:invitees,id'],
        ]);

        // Get invitees to send to
        $query = $assessment->invitees()->where('status', 'pending');
        
        if (!empty($validated['invitee_ids'])) {
            $query->whereIn('id', $validated['invitee_ids']);
        }

        $invitees = $query->get();

        if ($invitees->isEmpty()) {
            return response()->json([
                'message' => 'No pending invitees to send to.',
                'sent' => 0,
            ]);
        }

        // Queue emails in batches (Migadu limit)
        $batchSize = 50;
        $batches = $invitees->chunk($batchSize);
        $totalSent = 0;

        foreach ($batches as $batchIndex => $batch) {
            $delay = $batchIndex * 60; // 60 second delay between batches

            dispatch(function () use ($batch, $assessment) {
                foreach ($batch as $invitee) {
                    $this->sendInviteEmail($invitee, $assessment);
                }
            })->delay(now()->addSeconds($delay));

            $totalSent += $batch->count();
        }

        return response()->json([
            'message' => 'Invitations queued for sending.',
            'sent' => $totalSent,
            'batches' => $batches->count(),
        ]);
    }

    protected function sendInviteEmail(Invitee $invitee, Assessment $assessment): void
    {
        try {
            // This would use a Mailable class in production
            $link = config('app.url') . '/test/' . $invitee->invite_token;
            
            Mail::raw(
                "You have been invited to take the assessment: {$assessment->title}\n\n" .
                "Click here to start: {$link}\n\n" .
                "This assessment is available from {$assessment->start_datetime->format('M j, Y g:i A')} " .
                "to {$assessment->end_datetime->format('M j, Y g:i A')}.\n\n" .
                "Duration: {$assessment->duration_minutes} minutes",
                function ($message) use ($invitee, $assessment) {
                    $message->to($invitee->email)
                        ->subject("You're invited: {$assessment->title}");
                }
            );

            $invitee->markAsSent();
        } catch (\Exception $e) {
            logger()->error('Failed to send invite email', [
                'invitee_id' => $invitee->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function parseCSV($file): array
    {
        $emails = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        $rowCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if ($rowCount++ > 10000) {
                break; // Safety limit
            }

            foreach ($row as $cell) {
                $email = strtolower(trim($cell));
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $email;
                }
            }
        }
        
        fclose($handle);
        return array_unique($emails);
    }

    protected function getOwnedAssessment(Request $request, string $assessmentId): Assessment
    {
        return Assessment::where('id', $assessmentId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }
}
