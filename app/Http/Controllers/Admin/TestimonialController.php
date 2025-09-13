<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('created_at', 'desc')->get();
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|max:255',
            'client_position' => 'nullable|max:255',
            'client_company' => 'nullable|max:255',
            'content' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'project_name' => 'nullable|max:255',
            'date' => 'nullable|date',
            'featured' => 'boolean',
            'published' => 'boolean',
        ]);

        $validated['featured'] = $request->has('featured');
        $validated['published'] = $request->has('published');

        Testimonial::create($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage créé avec succès.');
    }

    public function show(Testimonial $testimonial)
    {
        return view('admin.testimonials.show', compact('testimonial'));
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'client_name' => 'required|max:255',
            'client_position' => 'nullable|max:255',
            'client_company' => 'nullable|max:255',
            'content' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'project_name' => 'nullable|max:255',
            'date' => 'nullable|date',
            'featured' => 'boolean',
            'published' => 'boolean',
        ]);

        $validated['featured'] = $request->has('featured');
        $validated['published'] = $request->has('published');

        $testimonial->update($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage mis à jour avec succès.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage supprimé avec succès.');
    }
}
