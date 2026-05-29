<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'username' => 'required|string|max:255|unique:users,name',
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string',
            'password' => 'required|string|min:6',
            'divisi_id' => 'nullable|exists:divisi,id',
        ];

        $messages = [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal harus 6 karakter.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Mengembalikan semua error sebagai JSON karena form dihandle oleh AJAX
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            User::create([
                'name' => $request->username, // Menggunakan 'username' dari form sebagai 'name'
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'divisi_id' => $request->role === 'admin' ? null : $request->divisi_id, // Set divisi_id jika role bukan admin
            ]);

            // Mengembalikan JSON karena form dihandle oleh AJAX
            return response()->json(['success' => true, 'message' => 'Pengguna berhasil ditambahkan.']);
        } catch (\Exception $e) {
            // Jika terjadi error saat menyimpan, kembalikan JSON error
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan pengguna: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // ✅ Tambahan: Kembalikan JSON jika request adalah AJAX/API
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'divisi_id' => $user->divisi_id,
                ]
            ]);
        }
        return view('users.show', compact('user')); // Untuk view biasa jika ada
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Aturan validasi
        $rules = [
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($user->id)],
            'full_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:user,admin',
            'password' => 'nullable|string|min:6|confirmed',
            'divisi_id' => 'required_if:role,user|nullable|exists:divisi,id',
        ];

        $messages = [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'divisi_id.required_if' => 'Divisi wajib diisi untuk role user.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        
        // Jika validasi gagal, kembalikan error sebagai JSON
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Update data pengguna
        $updateData = [
            'name' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'role' => $request->role,
            'divisi_id' => $request->role === 'admin' ? null : $request->divisi_id,
        ];

        $user->update($updateData);

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Kembalikan response JSON
        return response()->json(['success' => true, 'message' => 'Pengguna berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        // Cek jika request adalah AJAX
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pengguna berhasil dihapus.']);
        }
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Import users from CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $data = array_map('str_getcsv', file($path));

        // Skip header row
        array_shift($data);

        foreach ($data as $row) {
            if (count($row) >= 4) {
                User::create([
                    'name' => $row[0],
                    'email' => $row[1],
                    'password' => Hash::make($row[2]),
                    'role' => $row[3],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Users imported successfully.');
    }
}
