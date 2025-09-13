<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        // On récupère tous les projets publiés
        $projects = Project::published()->orderBy('order', 'asc')->get();

        return view('projects.index', compact('projects'));
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        // On récupère 3 projets connexes différents du projet actuel
        $relatedProjects = Project::where('id', '!=', $project->id)
                                ->inRandomOrder()
                                ->take(3)
                                ->get();

        return view('projects.show', compact('project', 'relatedProjects'));
    }
}
