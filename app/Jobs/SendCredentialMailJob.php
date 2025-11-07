<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCredentialMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $student;
    protected $password;
    protected $loginUrl;

    public function __construct($student, $password)
    {
        $this->student = $student;
        $this->password = $password;
        $this->loginUrl = route('student.login');
    }

    public function handle(): void
    {
        Mail::send('emails.student-credentials', [
            'password' => $this->password,
            'student' => $this->student,
            'loginUrl' => $this->loginUrl,
        ], function ($message) {
            $message->to($this->student->email)
                ->subject('Your Student Portal Login Credentials');
        });
    }
}
