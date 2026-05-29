<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Update the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'sid' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'divisi_id' => 'nullable|exists:divisi,id',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'full_name' => $validated['name'], // Asumsikan name dan full_name sama
            'email' => $validated['email'],
            'sid' => $validated['sid'],
            'phone' => $validated['phone'],
            'bio' => $validated['bio'],
        ];

        // Hanya update divisi_id jika user bukan admin
        if ($user->role !== 'admin' && isset($validated['divisi_id'])) {
            $updateData['divisi_id'] = $validated['divisi_id'];
        }

        if ($request->hasFile('profile_picture')) {
            // Hapus gambar lama jika ada
            if ($user->profile_picture && \Storage::disk('public')->exists($user->profile_picture)) {
                \Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        $user->update($updateData);

        return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
    }
}