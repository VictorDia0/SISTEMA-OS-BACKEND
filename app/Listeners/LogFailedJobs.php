<?php

namespace App\Listeners;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;

class LogFailedJobs
{
    public function handle(JobFailed $event)
    {
        Log::error('Falha no job da fila: ' . $event->job->resolveName(), [
            'exception' => $event->exception->getMessage(),
            'trace' => $event->exception->getTraceAsString(),
            'payload' => $event->job->payload(),
        ]);
    }
}
