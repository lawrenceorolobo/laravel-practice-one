<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;
    public int $maxExceptions = 2;
    public array $backoff = [5, 15];

    public function __construct(
        public string $to,
        public Mailable $mailable,
    ) {}

    public function handle(): void
    {
        Mail::to($this->to)->send($this->mailable);
    }

    public function failed(\Throwable $e): void
    {
        logger()->error('Email job failed', [
            'to' => $this->to,
            'mailable' => get_class($this->mailable),
            'error' => $e->getMessage(),
        ]);
    }
}
