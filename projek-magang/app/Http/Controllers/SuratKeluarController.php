<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuratKeluar;
use App\Models\Divisi;

class SuratKeluarController extends Controller
{
    /**
     * Mendapatkan warna untuk status
     *
     * @param string $status
     * @return string
     */
    private function getStatusColor($status)
    {
        $colors = [
            'baru' => 'primary',
            'diterima' => 'success',
            'ditolak' => 'danger',
            'diproses' => 'warning text-dark',
            'selesai' => 'info',
            'draft' => 'secondary',
            'dikirim' => 'info',
        ];

        return $colors[$status] ?? 'secondary';
    }

    /**
     * Mendapatkan nama untuk status
     *
     * @param string $status
     * @return string
     */
    private function getStatusName($status)
    {
        $names = [
            'baru' => 'Baru',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'draft' => 'Draft',
            'dikirim' => 'Dikirim',
        ];

        return $names[$status] ?? $status;
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        // Memulai query builder untuk SuratKeluar, diurutkan dari yang terbaru
        $query = SuratKeluar::with(['divisi', 'formatFile'])->latest('tanggal_kirim');

        // Terapkan filter pencarian berdasarkan 'penerima' jika ada
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('penerima', 'like', '%' . $searchTerm . '%')
                  ->orWhere('judul_laporan', 'like', '%' . $searchTerm . '%')
                  ->orWhere('perihal', 'like', '%' . $searchTerm . '%');
            });
        }

        // Terapkan filter berdasarkan 'divisi_id' jika ada
        if ($request->filled('divisi')) {
            $query->where('divisi_id', $request->divisi);
        }

        // Terapkan filter berdasarkan 'status' jika ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Cek apakah ini adalah request AJAX dari script filter
        if ($request->ajax() || $request->wantsJson()) {
            $suratKeluar = $query->get();
            return response()->json(['success' => true, 'suratKeluar' => $suratKeluar]);
        }

        // Ambil semua divisi untuk dropdown filter
        $divisi = Divisi::all();

        // Cek apakah ini adalah request AJAX dari script filter
        if ($request->ajax()) {
            // Jika ya, kirim kembali hanya view dari halaman surat keluar.
            $suratKeluar = $query->get();
            return response()->json(['success' => true, 'suratKeluar' => $suratKeluar]);
        }

        // Jika ini adalah permintaan awal (bukan AJAX), kembalikan view utama.
        // Ambil data dengan paginasi untuk tampilan awal
        $suratKeluar = $query->paginate(10)->appends($request->except('page'));
        return view('dashboard.index', [
            'active' => 'surat-keluar',
            'suratKeluar' => $suratKeluar,
            'divisi' => $divisi,
        ]);
    }

    /**
     * Store a newly created SuratKeluar in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomor_surat' => 'required|string|max:255',
                'judul_laporan' => 'required|string|max:255',
                'perihal' => 'required|string|max:255',
                'penerima' => 'required|string|max:255',
                'divisi_id' => 'required|exists:divisi,id',
                'tanggal_kirim' => 'required|date_format:Y-m-d',
                'status' => 'nullable|string|max:50',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
                'instruksi_disposisi' => 'nullable|string',
                'instruksi_tambahan' => 'nullable|string',
                'lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            ], [
                'divisi_id.required' => 'Divisi harus dipilih.',
                'divisi_id.exists' => 'Divisi yang dipilih tidak valid.',
            ]);

            $surat = new SuratKeluar();
$surat->nomor_surat = $validated['nomor_surat'];
$surat->judul_laporan = $validated['judul_laporan'];
$surat->perihal = $validated['perihal'];
$surat->instruksi_disposisi = $validated['instruksi_disposisi'] ?? null;
$surat->instruksi_tambahan = $validated['instruksi_tambahan'] ?? null;
$surat->penerima = $validated['penerima'];
$surat->divisi_id = $validated['divisi_id'];
$surat->tanggal_kirim = $validated['tanggal_kirim'];
            $surat->status = $request->input('status', 'dikirim'); // Default status 'dikirim'
            $surat->user_id = Auth::id();
            $surat->created_by = Auth::id();

            // Handle file upload
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $path = $request->file('file')->store('surat_keluar', 'public');
                $surat->file_path = $path;

                // Tentukan format_file_id berdasarkan ekstensi file
                $extension = strtolower($request->file('file')->getClientOriginalExtension());
                if (in_array($extension, ['xlsx', 'xls'])) {
                    $surat->format_file_id = 3; // ID untuk XLSX/Excel
                } elseif (in_array($extension, ['docx', 'doc'])) {
                    $surat->format_file_id = 2; // ID untuk DOCX/Word
                } else {
                    $surat->format_file_id = 1; // Default ke PDF
                }
            }

            // Handle lampiran upload
            if ($request->hasFile('lampiran') && $request->file('lampiran')->isValid()) {
                $lampiranPath = $request->file('lampiran')->store('surat_keluar_lampiran', 'public');
                $surat->lampiran_path = $lampiranPath;
            }

            $surat->save();

            // Selalu kembalikan JSON karena form di-handle oleh JavaScript
            return response()->json(['success' => true, 'message' => 'Surat keluar berhasil ditambahkan.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratKeluar $suratKeluar)
    {
        if (!$suratKeluar) {
            // Cek jika request adalah AJAX
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Surat keluar tidak ditemukan.'], 404);
            }
            return response()->json(['success' => false, 'message' => 'Surat keluar tidak ditemukan.'], 404);
        }

        // Format data for frontend
$formattedSurat = [
            'id' => $suratKeluar->id,
            'nomor_surat' => $suratKeluar->nomor_surat,
            'judul_laporan' => $suratKeluar->judul_laporan,
            'penerima' => $suratKeluar->penerima,
            'tanggal_kirim' => $suratKeluar->tanggal_kirim->format('Y-m-d'),
            'instruksi_disposisi' => $suratKeluar->instruksi_disposisi,
            'instruksi_tambahan' => $suratKeluar->instruksi_tambahan,
            'perihal' => $suratKeluar->perihal,  // added this missing field
            'divisi_name' => $suratKeluar->divisi->nama_divisi ?? ($suratKeluar->nama_divisi ?? 'N/A'),
            'divisi_id' => $suratKeluar->divisi->id ?? $suratKeluar->divisi_id ?? null,
            'status' => $suratKeluar->status,
            'status_name' => $this->getStatusName($suratKeluar->status),
            'status_color' => $this->getStatusColor($suratKeluar->status),
            'format_name' => $suratKeluar->formatFile->nama_format ?? ($suratKeluar->format_file_id ? 'File' : 'N/A'),
            'format_file_id' => $suratKeluar->format_file_id,
            'user_name' => $suratKeluar->user ? $suratKeluar->user->name : 'N/A',
            'file_path' => $suratKeluar->file_path,
        ];

        return response()->json(['success' => true, 'surat' => $formattedSurat]);
    }

    /**
     * Update the specified resource in storage.
     */
    
    public function update(Request $request, SuratKeluar $suratKeluar)
    {
        try {
            // Transform date format before validation
            if ($request->has('tanggal_kirim')) {
                // Coba parse d/m/Y, jika gagal, asumsikan sudah Y-m-d
                try {
                    $request->merge(['tanggal_kirim' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_kirim)->format('Y-m-d')]);
                } catch (\Exception $e) {
                    // Biarkan, mungkin sudah dalam format Y-m-d
                }
            }

            $validated = $request->validate([
                'nomor_surat' => 'required|string|max:255',
                'judul_laporan' => 'required|string|max:255',
                'perihal' => 'required|string|max:255',
                'penerima' => 'required|string|max:255',
                'divisi_id' => 'required|exists:divisi,id',
                'tanggal_kirim' => 'required|date',
                'status' => 'required|string|max:50',
                'format_file_id' => 'nullable|integer|exists:format_file,id', // Dibuat nullable karena akan diisi otomatis
                'file' => 'nullable|file|mimes:pdf,docx,xlsx|max:10240', // 10MB PDF, DOCX, XLSX
                'instruksi_disposisi' => 'nullable|string',
                'instruksi_tambahan' => 'nullable|string',
                'lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            ], [
                'divisi_id.required' => 'Divisi harus dipilih.',
                'divisi_id.exists' => 'Divisi yang dipilih tidak valid.',
            ]);

$updateData = $validated;

$updateData['instruksi_disposisi'] = $validated['instruksi_disposisi'] ?? null;
$updateData['instruksi_tambahan'] = $validated['instruksi_tambahan'] ?? null;

            // Handle file upload
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                // Hapus file lama jika ada
                if ($suratKeluar->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($suratKeluar->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($suratKeluar->file_path);
                }
                $path = $request->file('file')->store('surat_keluar', 'public');
                $updateData['file_path'] = $path;

                // Tentukan format_file_id berdasarkan ekstensi file
                $extension = strtolower($request->file('file')->getClientOriginalExtension());
                if (in_array($extension, ['xlsx', 'xls'])) {
                    $updateData['format_file_id'] = 3; // ID untuk XLSX/Excel
                } elseif (in_array($extension, ['docx', 'doc'])) {
                    $updateData['format_file_id'] = 2; // ID untuk DOCX/Word
                } else {
                    $updateData['format_file_id'] = 1; // Default ke PDF
                }
            } elseif (isset($validated['format_file_id'])) {
                $updateData['format_file_id'] = $validated['format_file_id'];
            }

            // Handle lampiran upload
            if ($request->hasFile('lampiran') && $request->file('lampiran')->isValid()) {
                // Hapus file lama jika ada
                if ($suratKeluar->lampiran_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($suratKeluar->lampiran_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($suratKeluar->lampiran_path);
                }
                $updateData['lampiran_path'] = $request->file('lampiran')->store('surat_keluar_lampiran', 'public');
            }

            // Lakukan update
            $suratKeluar->update($updateData);

            // Format data for frontend response after update
            $formattedSurat = [
                'id' => $suratKeluar->id,
                'nomor_surat' => $suratKeluar->nomor_surat,
                'penerima' => $suratKeluar->penerima,
                'tanggal_kirim' => $suratKeluar->tanggal_kirim->format('Y-m-d'),
                'instruksi_disposisi' => $suratKeluar->instruksi_disposisi,
                'instruksi_tambahan' => $suratKeluar->instruksi_tambahan,
                'perihal' => $suratKeluar->perihal,  // assuming this was added to fill in missing field
                'divisi_name' => $suratKeluar->divisi->nama_divisi ?? ($suratKeluar->nama_divisi ?? 'N/A'),
                'divisi_id' => $suratKeluar->divisi->id ?? $suratKeluar->divisi_id ?? null,
                'status' => $suratKeluar->status,
                'status_name' => $this->getStatusName($suratKeluar->status),
                'status_color' => $this->getStatusColor($suratKeluar->status),
                'format_name' => $suratKeluar->formatFile->nama_format ?? ($suratKeluar->format_file_id ? 'File' : 'N/A'),
                'format_file_id' => $suratKeluar->format_file_id,
                'user_name' => $suratKeluar->user ? $suratKeluar->user->name : 'N/A',
                'file_path' => $suratKeluar->file_path,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Surat keluar berhasil diperbarui.',
                'surat' => $formattedSurat
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratKeluar $suratKeluar)
    {
        $suratKeluar->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Surat keluar berhasil dihapus.']);
        }

        return redirect()->route('dashboard', ['section' => 'surat-keluar'])->with('success', 'Surat keluar berhasil dihapus.');
    }
}
