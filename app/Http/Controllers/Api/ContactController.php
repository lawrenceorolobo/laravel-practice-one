<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Mail\ContactFormMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $supportEmail = config('mail.from.address', 'support@quizly.com');

        SendEmailJob::dispatch(
            $supportEmail,
            new ContactFormMail(
                $validated['name'],
                $validated['email'],
                $validated['subject'],
                $validated['message'],
            )
        );

        return response()->json([
            'message' => 'Message sent successfully! We\'ll get back to you within 24 hours.',
        ]);
    }
}
