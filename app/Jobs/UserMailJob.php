<?php

namespace App\Jobs;

use App\Mail\UserMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class UserMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $name;
    public $email;
    public function __construct($my_name, $my_email)
    {
        $this->name = $my_name;
        $this->email = $my_email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::send(new UserMail($this->name, $this->email));
    }
}
