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

        $mail = $this->subject('Your Order Confirmation - #' . $this->order->order_id)
            ->view('emails.order-customer');

        // START: Invoice PDF attachment (same template as the management Download Invoice button)
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('management.invoice', ['order' => $this->order]);
            $mail->attachData(
                $pdf->output(),
                'Invoice-Order-' . $this->order->order_id . '.pdf',
                ['mime' => 'application/pdf']
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Invoice PDF attachment failed for order #' . $this->order->order_id . ': ' . $e->getMessage());
        }
        // END: Invoice PDF attachment

        return $mail;
    }
}