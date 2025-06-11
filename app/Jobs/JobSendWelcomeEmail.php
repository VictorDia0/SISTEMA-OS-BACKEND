<?php

namespace App\Jobs;

use App\Mail\ValidationEmail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class JobSendWelcomeEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     * @return int $userId
     */
    public function __construct(private $userId)
    {
        //
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle(): void
    {
        $user =  User::find($this->userId);


        Mail::to($user->email)->send(new ValidationEmail($user));
    }
}
