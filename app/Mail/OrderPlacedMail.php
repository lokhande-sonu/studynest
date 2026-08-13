<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use SerializesModels;

    public $order;
    public $type; // customer or admin

    public function __construct($order, $type = 'customer')
    {
        $this->order = $order;
        $this->type = $type;
    }

    public function build()
    {
        if ($this->type === 'admin') {
            return $this->subject('New Order Received - #' . $this->order->order_id)
                ->view('emails.order-admin');
        }

        return $this->subject('Your Order Confirmation - #' . $this->order->order_id)
            ->view('emails.order-customer');
    }
}