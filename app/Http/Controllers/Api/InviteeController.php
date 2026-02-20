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
            ->with('testSession:id,invitee_id,status,percentage,passed,total_score,max_score,tab_switches,fullscreen_exits,time_spent_seconds,submitted_at')
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
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'csv' => ['required_without:emails', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        // Build rows: [{email, first_name, last_name}, ...]
        $rows = [];

        if (isset($validated['emails'])) {
            $seen = [];
            foreach ($validated['emails'] as $email) {
                $email = strtolower(trim($email));
                if (!isset($seen[$email])) {
                    $seen[$email] = true;
                    $rows[] = [
                        'email' => $email,
                        'first_name' => $validated['first_name'] ?? null,
                        'last_name' => $validated['last_name'] ?? null,
                    ];
                }
            }
        } elseif ($request->hasFile('csv')) {
            $rows = $this->parseCSV($request->file('csv'));
        }

        if (count($rows) > 1000) {
            throw ValidationException::withMessages([
                'emails' => ['Maximum 1000 invitees per batch.'],
            ]);
        }

        // Filter out already existing emails
        $emailList = array_map(fn ($r) => $r['email'], $rows);
        $existingEmails = $assessment->invitees()
            ->whereIn(DB::raw('LOWER(email)'), $emailList)
            ->pluck('email')
            ->map(fn ($e) => strtolower($e))
            ->toArray();

        $newRows = array_filter($rows, fn ($r) => !in_array($r['email'], $existingEmails));

        $created = 0;
        $skipped = count($existingEmails);
        $createdInvitees = [];

        DB::transaction(function () use ($assessment, $newRows, &$created, &$createdInvitees) {
            foreach ($newRows as $row) {
                $invitee = Invitee::create([
                    'assessment_id' => $assessment->id,
                    'email' => $row['email'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'invite_token' => Str::random(64),
                    'status' => 'pending',
                ]);
                $createdInvitees[] = $invitee;
                $created++;
            }

            $assessment->update(['total_invites' => $assessment->invitees()->count()]);
        });

        // Auto-send emails if assessment is already published
        if (in_array($assessment->status, ['scheduled', 'active']) && count($createdInvitees) > 0) {
            foreach ($createdInvitees as $invitee) {
                $invitee->update(['email_status' => 'queued']);
                dispatch(function () use ($assessment, $invitee) {
                    try {
                        Mail::to($invitee->email)->send(new \App\Mail\InvitationMail($assessment, $invitee));
                        $invitee->update(['email_status' => 'sent', 'email_sent_at' => now()]);
                    } catch (\Exception $e) {
                        $invitee->update(['email_status' => 'failed']);
                        \Log::warning("Auto-send failed for {$invitee->email}: " . $e->getMessage());
                    }
                });
            }
        }
        // Return error if ALL were duplicates
        if ($created === 0 && $skipped > 0) {
            return response()->json([
                'message' => $skipped === 1
                    ? 'This email is already invited.'
                    : "All {$skipped} email(s) are already invited.",
                'created' => 0,
                'skipped' => $skipped,
            ], 409);
        }

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
     * Update invitee details
     */
    public function update(Request $request, string $assessmentId, string $inviteeId): JsonResponse
    {
        $assessment = $this->getOwnedAssessment($request, $assessmentId);

        $invitee = $assessment->invitees()
            ->where('id', $inviteeId)
            ->firstOrFail();

        if ($invitee->hasStarted()) {
            throw ValidationException::withMessages([
                'invitee' => ['Cannot edit invitee who has already started the test.'],
            ]);
        }

        $validated = $request->validate([
            'email' => ['sometimes', 'email', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
        ]);

        // Check for duplicate email if changing
        if (isset($validated['email'])) {
            $email = strtolower(trim($validated['email']));
            $exists = $assessment->invitees()
                ->where('id', '!=', $invitee->id)
                ->where(DB::raw('LOWER(email)'), $email)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'email' => ['This email is already invited to this assessment.'],
                ]);
            }
            $validated['email'] = $email;
        }

        $invitee->update($validated);

        return response()->json([
            'message' => 'Invitee updated successfully.',
            'invitee' => $invitee->fresh(),
        ]);
    }

    /**
     * Resend invitation email to a single invitee (async — returns instantly)
     */
    public function resend(Request $request, string $assessmentId, string $inviteeId): JsonResponse
    {
        $assessment = $this->getOwnedAssessment($request, $assessmentId);

        if (!in_array($assessment->status, ['scheduled', 'active'])) {
            throw ValidationException::withMessages([
                'assessment' => ['Assessment must be published before sending invites.'],
            ]);
        }

        $invitee = $assessment->invitees()
            ->where('id', $inviteeId)
            ->firstOrFail();

        if ($invitee->hasCompleted()) {
            throw ValidationException::withMessages([
                'invitee' => ['Cannot resend to invitee who has completed the test.'],
            ]);
        }

        $invitee->update(['email_status' => 'queued']);

        // Dispatch to queue — returns immediately
        dispatch(function () use ($assessment, $invitee) {
            try {
                Mail::to($invitee->email)->send(new \App\Mail\InvitationMail($assessment, $invitee));
                $invitee->update(['email_status' => 'sent', 'email_sent_at' => now()]);
            } catch (\Exception $e) {
                $invitee->update(['email_status' => 'failed']);
                \Log::warning("Resend failed for {$invitee->email}: " . $e->getMessage());
            }
        });

        return response()->json([
            'message' => 'Invitation sent for ' . $invitee->email,
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

        // Get invitees who haven't started/completed the test
        $query = $assessment->invitees()->whereNotIn('status', ['completed']);
        
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
            'message' => 'Invitations sent.',
            'sent' => $totalSent,
            'batches' => $batches->count(),
        ]);
    }

    protected function sendInviteEmail(Invitee $invitee, Assessment $assessment): void
    {
        try {
            Mail::to($invitee->email)->send(new \App\Mail\InvitationMail($assessment, $invitee));
            $invitee->markAsSent();
        } catch (\Exception $e) {
            logger()->error('Failed to send invite email', [
                'invitee_id' => $invitee->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Parse CSV file into invitee rows with email, first_name, last_name.
     * Supports: email-only, email+first_name, or email+first_name+last_name columns.
     * Auto-detects header row.
     */
    protected function parseCSV($file): array
    {
        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');
        $seen = [];
        $rowCount = 0;
        $headerSkipped = false;

        while (($row = fgetcsv($handle)) !== false) {
            if ($rowCount++ > 10000) {
                break;
            }

            // Skip header row if first cell looks like a header
            if (!$headerSkipped) {
                $headerSkipped = true;
                $firstCell = strtolower(trim($row[0] ?? ''));
                if (in_array($firstCell, ['email', 'email_address', 'e-mail', 'mail'])) {
                    continue;
                }
            }

            $email = strtolower(trim($row[0] ?? ''));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if (isset($seen[$email])) {
                continue;
            }
            $seen[$email] = true;

            $rows[] = [
                'email' => $email,
                'first_name' => trim($row[1] ?? '') ?: null,
                'last_name' => trim($row[2] ?? '') ?: null,
            ];
        }

        fclose($handle);
        return $rows;
    }

    protected function getOwnedAssessment(Request $request, string $assessmentId): Assessment
    {
        return Assessment::where('id', $assessmentId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }
}
