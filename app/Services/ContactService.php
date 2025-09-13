<?php

namespace App\Services;

use App\Models\Contact;
use App\Mail\ContactReceived;
use App\Mail\ContactResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactService
{
    /**
     * Process incoming contact message
     */
    public function processIncomingMessage(array $data): Contact
    {
        // Save to database
        $contact = Contact::create($data);

        try {
            // Send confirmation to sender
            Mail::to($data['email'])->send(new ContactReceived($contact));

            // Send notification to admin
            $adminEmail = config('mail.admin_email', env('MAIL_ADMIN_ADDRESS', 'admin@novatechbenin.com'));
            Mail::to($adminEmail)->send(new ContactReceived($contact, true));

        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
        }

        return $contact;
    }

    /**
     * Send response to contact
     */
    public function sendResponse(Contact $contact, string $response): bool
    {
        try {
            // Send response email
            Mail::to($contact->email)->send(new ContactResponse($contact, $response));

            // Update contact record
            $contact->update([
                'response' => $response,
                'response_sent_at' => now(),
                'status' => Contact::STATUS_RESOLVED
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Response email failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get contact statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Contact::count(),
            'unread' => Contact::where('read', false)->count(),
            'new' => Contact::where('status', Contact::STATUS_NEW)->count(),
            'in_progress' => Contact::where('status', Contact::STATUS_IN_PROGRESS)->count(),
            'resolved' => Contact::where('status', Contact::STATUS_RESOLVED)->count(),
        ];
    }
}
