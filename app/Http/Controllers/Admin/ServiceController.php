<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('order')->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'features' => 'nullable',
            'icon' => 'nullable|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'nullable|integer',
            'published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['published'] = $request->has('published');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/service'), $imageName);
            $validated['image'] = 'images/service/' . $imageName;
        }

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service créé avec succès.');
    }

    public function togglePublished(Service $service)
    {
        $service->update(['published' => !$service->published]);

        return back()->with('success', 'Le statut de publication a été mis à jour.');
    }

    public function show(Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'features' => 'nullable',
            'icon' => 'nullable|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'nullable|integer',
            'published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['published'] = $request->has('published');

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($service->image && File::exists(public_path($service->image))) {
                File::delete(public_path($service->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/service'), $imageName);
            $validated['image'] = 'images/service/' . $imageName;
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service mis à jour avec succès.');
    }

    public function destroy(Service $service)
    {
        if ($service->image && File::exists(public_path($service->image))) {
            File::delete(public_path($service->image));
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service supprimé avec succès.');
    }
}
