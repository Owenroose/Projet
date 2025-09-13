<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // <-- LIGNE AJOUTÉE
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Affiche la vue d'édition du profil de l'utilisateur.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // On récupère l'utilisateur actuellement authentifié
        $user = Auth::user(); // <-- MODIFIÉ
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Met à jour le profil de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
   public function update(Request $request)
{
    // Logique pour valider et mettre à jour le profil ou le mot de passe
    $user = Auth::user();

    // Si le champ 'current_password' est rempli, nous supposons que l'utilisateur veut changer de mot de passe
    if ($request->filled('current_password')) {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Votre mot de passe a été mis à jour avec succès.');
    } else {
        // Sinon, nous mettons à jour les informations de base du profil
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->fill($validated);
        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
}
