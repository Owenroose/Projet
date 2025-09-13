<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $projects = Project::orderBy('order')->get();
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'challenge' => 'nullable',
            'solution' => 'nullable',
            'technologies' => 'nullable',
            'client' => 'nullable|max:255',
            'project_date' => 'nullable|date',
            'project_url' => 'nullable|url',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'nullable|integer',
            'featured' => 'boolean',
            'published' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Correction : Utiliser le chemin 'images/projets'
            $validated['image'] = $request->file('image')->store('images/projets', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['featured'] = $request->has('featured');
        $validated['published'] = $request->has('published');

        Project::create($validated);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projet créé avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'challenge' => 'nullable',
            'solution' => 'nullable',
            'technologies' => 'nullable',
            'client' => 'nullable|max:255',
            'project_date' => 'nullable|date',
            'project_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'nullable|integer',
            'featured' => 'boolean',
            'published' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($project->image) {
                // Correction : Supprimer l'ancienne image depuis le bon chemin
                Storage::disk('public')->delete($project->image);
            }
            // Correction : Utiliser le chemin 'images/projets'
            $validated['image'] = $request->file('image')->store('images/projets', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['featured'] = $request->has('featured');
        $validated['published'] = $request->has('published');

        $project->update($validated);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project)
    {
        if ($project->image) {
            // Correction : Supprimer l'image depuis le bon chemin
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projet supprimé avec succès.');
    }
}
