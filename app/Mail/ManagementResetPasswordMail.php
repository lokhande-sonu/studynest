<?php

// app/Mail/ManagementResetPasswordMail.php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManagementResetPasswordMail extends Mailable
{
    use SerializesModels;

    public $resetLink;
    public $email;

    public function __construct($email, $resetLink)
    {
        $this->email = $email;
        $this->resetLink = $resetLink;
    }

    public function build()
    {
        return $this->subject('Reset Your Employing Bulls Management Panel Password')
            ->view('emails.reset-password');
    }
}
