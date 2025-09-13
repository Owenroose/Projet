<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TeamMemberController extends Controller
{
    public function index()
    {
        $teamMembers = TeamMember::orderBy('order')->get();
        return view('admin.team.index', compact('teamMembers'));
    }

    public function create()
    {
        return view('admin.team.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'position' => 'required|max:255',
            'bio' => 'nullable',
            'skills' => 'nullable',
            'experience' => 'nullable|integer|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'order' => 'nullable|integer',
            'published' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/teams'), $imageName);
            $validated['photo'] = 'images/teams/' . $imageName;
        }

        $validated['published'] = $request->has('published');

        TeamMember::create($validated);

        return redirect()->route('admin.team.index')
            ->with('success', 'Membre d\'équipe créé avec succès.');
    }

    public function show(TeamMember $teamMember)
    {
        return view('admin.team.show', compact('teamMember'));
    }

    public function edit(TeamMember $teamMember)
    {
        return view('admin.team.edit', compact('teamMember'));
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'position' => 'required|max:255',
            'bio' => 'nullable',
            'skills' => 'nullable',
            'experience' => 'nullable|integer|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'order' => 'nullable|integer',
            'published' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($teamMember->photo && File::exists(public_path($teamMember->photo))) {
                File::delete(public_path($teamMember->photo));
            }
            $image = $request->file('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/teams'), $imageName);
            $validated['photo'] = 'images/teams/' . $imageName;
        }

        $validated['published'] = $request->has('published');

        $teamMember->update($validated);

        return redirect()->route('admin.team.index')
            ->with('success', 'Membre d\'équipe mis à jour avec succès.');
    }

    public function destroy(TeamMember $teamMember)
    {
        if ($teamMember->photo && File::exists(public_path($teamMember->photo))) {
            File::delete(public_path($teamMember->photo));
        }

        $teamMember->delete();

        return redirect()->route('admin.team.index')
            ->with('success', 'Membre d\'équipe supprimé avec succès.');
    }
}
