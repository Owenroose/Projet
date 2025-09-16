<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $orderGroup;
    public $status;
    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct($orderGroup, $status)
    {
        $this->orderGroup = $orderGroup;
        $this->status = $status;
        $this->order = Order::where('order_group', $orderGroup)->first();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mise à jour de votre commande ' . substr($this->orderGroup, 0, 8),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_status',
            with: [
                'order' => $this->order,
                'status' => $this->status,
                'trackLink' => route('order.track', ['order_group' => $this->orderGroup]) // Créez cette route
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
