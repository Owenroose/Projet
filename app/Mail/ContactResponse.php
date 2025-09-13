<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactResponse extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;
    public $response;

    public function __construct(Contact $contact, string $response)
    {
        $this->contact = $contact;
        $this->response = $response;
    }

    public function build()
    {
        return $this->subject('Réponse à votre message - Nova Tech Bénin')
                    ->markdown('emails.contact.response')
                    ->with([
                        'contact' => $this->contact,
                        'response' => $this->response
                    ]);
    }
}
