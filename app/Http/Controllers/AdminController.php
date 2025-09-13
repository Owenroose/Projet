<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Affiche le tableau de bord de l'administrateur.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupération des données pour les cartes de statistiques
        $stats = [
            'services' => Service::count(),
            'products' => Product::count(),
            'projects' => Project::count(),
            'team_members' => TeamMember::count(),
            'testimonials' => Testimonial::count(),
            'unread_contacts' => Contact::where('read', false)->count(),
        ];

        // Récupération des données pour le graphique
        $chartData = [
            'services' => Service::count(),
            'products' => Product::count(),
            'projects' => Project::count(),
        ];

        $recentContacts = Contact::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'chartData', 'recentContacts'));
    }
}
