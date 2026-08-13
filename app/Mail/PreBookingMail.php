<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreBookingMail extends Mailable
{
    use SerializesModels;

    public $booking;
    public $type; // customer / admin

    public function __construct($booking, $type = 'customer')
    {
        $this->booking = $booking;
        $this->type = $type;
    }

    public function build()
    {
        if ($this->type === 'admin') {
            return $this->subject('New Pre-Booking Request')
                ->view('emails.prebooking-admin');
        }

        return $this->subject('Pre-Booking Confirmation')
            ->view('emails.prebooking-customer');
    }
}