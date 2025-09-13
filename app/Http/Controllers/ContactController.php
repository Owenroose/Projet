<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    /**
     * Affiche le formulaire de contact.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('contact');
    }

    /**
     * Traite l'envoi du formulaire de contact.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        try {
            $key = 'contact-form:' . $request->ip();

            if (RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = RateLimiter::availableIn($key);

                return redirect()->back()
                    ->with('error', "Trop de tentatives. Veuillez réessayer dans " . ceil($seconds / 60) . " minutes.")
                    ->withInput();
            }

            $validator = $this->validateContactForm($request);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validatedData = $validator->validated();

            // Enregistrement du message dans la base de données
            $contact = Contact::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'] ?? null,
                'subject' => $validatedData['subject'],
                'message' => $validatedData['message'],
                'read' => false,
                'status' => Contact::STATUS_NEW,
                'priority' => Contact::PRIORITY_MEDIUM, // Priorité par défaut
            ]);

            // Envoi de l'e-mail de notification
            // Remplacez 'votreadresse@email.com' par votre adresse e-mail
            // Mail::to('votreadresse@email.com')->send(new ContactMessageReceived($contact));

            RateLimiter::hit($key);

            return redirect()->back()->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du formulaire de contact: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer plus tard.')
                ->withInput();
        }
    }

    /**
     * Valide les données du formulaire de contact.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateContactForm(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'g-recaptcha-response' => 'nullable|recaptcha',
        ]);
    }
}
