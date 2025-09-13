<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::published()->orderBy('order')->take(4)->get();
        $projects = Project::published()->featured()->orderBy('order')->take(6)->get();
        $testimonials = Testimonial::published()->featured()->orderBy('date', 'desc')->take(3)->get();
        $featuredProducts = Product::published()->inStock()->orderBy('created_at', 'desc')->take(6)->get();

        return view('home', compact('services', 'projects', 'testimonials', 'featuredProducts'));
    }
}
