<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index(Request $request)
    {
        $query = Contact::with('assignedTo')->latest();

        // Filtres
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        if ($request->has('read') && $request->read !== 'all') {
            $query->where('read', $request->read === 'read');
        }

        $contacts = $query->paginate(20);
        $stats = $this->contactService->getStatistics();

        return view('admin.contacts.index', compact('contacts', 'stats'));
    }

    public function show(Contact $contact)
    {
        if (!$contact->read) {
            $contact->markAsRead();
        }

        $users = User::where('is_active', true)->get();
        $statusOptions = Contact::getStatusOptions();
        $priorityOptions = Contact::getPriorityOptions();

        return view('admin.contacts.show', compact('contact', 'users', 'statusOptions', 'priorityOptions'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Message supprimé avec succès.');
    }

    public function markAsRead(Contact $contact)
    {
        $contact->markAsRead();

        return redirect()->route('admin.contacts.show', $contact)
            ->with('success', 'Message marqué comme lu.');
    }

    public function markAsUnread(Contact $contact)
    {
        $contact->markAsUnread();

        return redirect()->route('admin.contacts.show', $contact)
            ->with('success', 'Message marqué comme non lu.');
    }

    public function sendResponse(Request $request, Contact $contact)
    {
        $request->validate(['response' => 'required|string']);

        $contact->update([
            'response' => $request->response,
            'response_sent_at' => now(),
            'status' => Contact::STATUS_RESOLVED,
        ]);

        try {
            // Logique d'envoi d'email pour la réponse
            // Mail::to($contact->email)->send(new ContactReply($contact));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la réponse par email: ' . $e->getMessage());
        }

        return redirect()->back()
            ->with('success', 'Réponse envoyée et message marqué comme résolu.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:read,unread,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:contacts,id'
        ]);

        $action = $request->action;
        $ids = $request->ids;

        switch ($action) {
            case 'read':
                Contact::whereIn('id', $ids)->update(['read' => true]);
                $message = 'Messages marqués comme lus.';
                break;
            case 'unread':
                Contact::whereIn('id', $ids)->update(['read' => false]);
                $message = 'Messages marqués comme non lus.';
                break;
            case 'delete':
                Contact::whereIn('id', $ids)->delete();
                $message = 'Messages supprimés avec succès.';
                break;
        }

        return redirect()->route('admin.contacts.index')
            ->with('success', $message);
    }
}
