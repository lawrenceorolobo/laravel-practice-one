<?php

namespace App\Events;

use App\Models\Assessment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssessmentCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Assessment $assessment,
        public array $resultData
    ) {}
}
