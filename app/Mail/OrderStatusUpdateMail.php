<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends Mailable
{
    use SerializesModels;

    public $order;
    public $status;
    public $updatedAt;

    public function __construct($order, $status, $updatedAt)
    {
        $this->order = $order;
        $this->status = $status;
        $this->updatedAt = $updatedAt;
    }

    public function build()
    {
        return $this->subject('Order Status Updated - #' . $this->order->order_id)
            ->view('emails.order-status-update');
    }
}