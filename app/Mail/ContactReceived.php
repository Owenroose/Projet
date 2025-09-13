<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;
    public $isAdminNotification;

    public function __construct(Contact $contact, $isAdminNotification = false)
    {
        $this->contact = $contact;
        $this->isAdminNotification = $isAdminNotification;
    }

    public function build()
    {
        if ($this->isAdminNotification) {
            return $this->subject('Nouveau message de contact - Nova Tech Bénin')
                        ->markdown('emails.contact.admin-notification')
                        ->with(['contact' => $this->contact]);
        }

        return $this->subject('Confirmation de réception - Nova Tech Bénin')
                    ->markdown('emails.contact.confirmation')
                    ->with(['contact' => $this->contact]);
    }
}
