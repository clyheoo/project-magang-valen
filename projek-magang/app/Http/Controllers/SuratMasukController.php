<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\SuratMasuk;
use App\Models\Divisi;
use App\Models\FormatFile;
use App\Models\RiwayatStatus;
use App\Events\SuratMasukUpdated;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SuratMasukController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        include_once app_path('Helpers/functions.php');

        try {
            // ✅ Pastikan relasi 'divisi' dan 'formatFile' dimuat (eager loading)
            $query = SuratMasuk::with(['divisi', 'formatFile']);

            // Filter untuk role user: hanya tampilkan surat yang dibuat oleh user tersebut
            if (Auth::user()->role !== 'admin') {
                $query->where('created_by', Auth::id());
            }

            // Filter perihal
            // Kolom 'perihal' sudah dihapus dari database, jadi filter ini di-nonaktifkan
            // if ($request->filled('perihal')) {
            //     $query->where('perihal', 'like', '%' . $request->perihal . '%');
            // }

            // Filter berdasarkan divisi_id jika ada input dari dropdown
            if ($request->filled('divisi')) {
                // Jika input adalah nama divisi (string), cari berdasarkan nama_divisi
                if (is_string($request->divisi) && !is_numeric($request->divisi)) {
                    $query->where('nama_divisi', 'like', '%' . $request->divisi . '%');
                } else {
                    // Jika input adalah ID (numeric), filter berdasarkan divisi_id
                    $query->where('divisi_id', $request->divisi);
                }
            }

            // Filter status
            if ($request->filled('status')) {
                $query->where('status', 'like', '%' . $request->status . '%');
            }

            // ✅ Ambil data surat dengan relasi divisi
            $suratMasuk = $query->orderBy('tanggal', 'desc')->get();

            // ✅ Ambil semua daftar divisi untuk dropdown
            $divisiList = Divisi::select('id', 'nama_divisi')->get();

            // Daftar status
            $statusList = [
                'baru' => 'Baru',
                'diterima' => 'Diterima',
                'ditolak' => 'Ditolak',
                'diproses' => 'Diproses',
                'selesai' => 'Selesai',
                'draft' => 'Draft',
                'dikirim' => 'Dikirim',
            ];

            // ✅ Kirim data ke view atau JSON untuk API
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'suratMasuk' => $suratMasuk,
                    'divisiList' => $divisiList,
                    'statusList' => $statusList,
                ]);
            }

            return view('surat_masuk.index', compact('suratMasuk', 'divisiList', 'statusList'));
        } catch (\Exception $e) {
            \Log::error('Error fetching surat masuk: ' . $e->getMessage());

            // Return the view with empty data and error message in session
            return redirect()->route('surat-masuk.index')->with('error', 'Terjadi kesalahan saat mengambil data surat masuk.');
        }
    }

    public function create()
    {
        // Limit divisi to 3 specific divisions by name
        // Sesuai permintaan: Kasubag TU, SMA, SMK
        $formatFiles = FormatFile::all();
        // Pastikan daftar divisi dikirim ke view agar select bekerja
        $divisi = Divisi::select('id', 'nama_divisi')->get();
        return view('surat_masuk.create', compact('divisi', 'formatFiles'));
    }

public function edit($id)
{
    try {
        // Eager load relasi yang dibutuhkan untuk menghindari N+1 query
        $surat = SuratMasuk::with(['divisi', 'formatFile'])->findOrFail($id);
        $divisi = Divisi::select('id', 'nama_divisi')->get();
        $formatFiles = FormatFile::select('id', 'nama_format')->get();
        // Jika request mengharapkan JSON (misalnya dari AJAX)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'surat' => [
                    'id' => $surat->id,
                    'nomor_surat' => $surat->nomor_surat,
                    'divisi_id' => $surat->divisi_id,
                    'tanggal' => $surat->tanggal ? \Carbon\Carbon::parse($surat->tanggal)->format('Y-m-d') : null,
                    'instruksi_disposisi' => $surat->instruksi_disposisi,
                    'instruksi_tambahan' => $surat->instruksi_tambahan,
                    'pengirim' => $surat->pengirim,
                    'format_file_id' => $surat->format_file_id,
                ],
            ]);
        }

        // Jika request biasa (dari browser), tampilkan view
        return view('surat_masuk.edit', compact('surat', 'divisi', 'formatFiles'));
    } catch (ModelNotFoundException $e) {
        \Log::warning('SuratMasuk not found for edit. ID: ' . $id);
        return redirect()->route('surat-masuk.index')->with('error', 'Surat Masuk dengan ID ' . $id . ' tidak ditemukan.');
    } catch (\Exception $e) {
        \Log::error('Error fetching surat masuk for edit. ID: ' . $id . ' - ' . $e->getMessage());
        return redirect()->route('surat-masuk.index')->with('error', 'Terjadi kesalahan saat mengambil data surat masuk dengan ID: ' . $id);
    }
}

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomor_surat' => 'required|string|max:255',
                'tanggal' => 'required|date_format:Y-m-d',
                'pengirim' => 'required|string|max:255',
                // 'format_file_id' => 'nullable|exists:format_file,id', // Dihapus karena diisi otomatis
                'divisi_id' => 'required|exists:divisi,id',
                'status' => 'nullable|string',
                'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB
                'instruksi_disposisi' => 'nullable|string',
                'instruksi_tambahan' => 'nullable|string',
            ], [
                'divisi_id.required' => 'Divisi harus dipilih.',
                'divisi_id.exists' => 'Divisi yang dipilih tidak valid.',
                'nomor_surat.required' => 'Nomor surat harus diisi.',
                'tanggal.required' => 'Tanggal harus diisi.',
                'pengirim.required' => 'Pengirim harus diisi.',
                // 'format_file_id.required' => 'Format file harus dipilih.', // Dihapus karena otomatis
                'file.required' => 'File harus diupload.',
                'file.mimes' => 'Format file harus PDF, DOC, DOCX, XLS, atau XLSX.',
                'file.max' => 'Ukuran file maksimal 10MB.',
            ]);

            // Debug: Log the request data
            \Log::info('Store request data:', $request->all());
            \Log::info('Validated data:', $validated);

            // Konversi tanggal secara manual sebelum membuat record
if (isset($validated['tanggal'])) {
    $validated['tanggal'] = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['tanggal']);
}

            // Set nama_divisi berdasarkan divisi_id yang dipilih
            if (!empty($validated['divisi_id'])) {
                $divisi = Divisi::where('id', $validated['divisi_id'])->first();
                if ($divisi) {
                    $validated['nama_divisi'] = $divisi->nama_divisi;
                }
            }

            // Upload file jika ada
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->store('surat_masuk', 'public');
                $validated['file_path'] = $filePath;
                $validated['ukuran'] = $file->getSize();

                // Tentukan format_file_id secara otomatis berdasarkan ekstensi file yang diunggah
                $extension = strtolower($file->getClientOriginalExtension());
                $formatFileId = null;

                switch ($extension) {
                    case 'pdf':
                        $formatFileId = \App\Models\FormatFile::where('nama_format', 'PDF')->value('id');
                        break;
                    case 'doc':
                    case 'docx':
                        $formatFileId = \App\Models\FormatFile::where('nama_format', 'DOCX')->value('id');
                        if (!$formatFileId) $formatFileId = \App\Models\FormatFile::where('nama_format', 'DOC')->value('id'); // Fallback jika DOCX tidak ditemukan, coba DOC
                        break;
                    case 'xls':
                    case 'xlsx':
                        $formatFileId = \App\Models\FormatFile::where('nama_format', 'XLSX')->value('id');
                        if (!$formatFileId) $formatFileId = \App\Models\FormatFile::where('nama_format', 'XLS')->value('id'); // Fallback jika XLSX tidak ditemukan, coba XLS
                        break;
                    default:
                        // Jika format tidak dikenali, format_file_id akan tetap null
                        break;
                }
                $validated['format_file_id'] = $formatFileId;
            }

            // Tambahkan ID pengguna yang membuat surat
            $validated['created_by'] = Auth::id();

            $surat = SuratMasuk::create($validated);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Surat masuk berhasil disimpan.',
                    'surat' => $surat // Menambahkan data surat yang baru dibuat ke respons
                ]);
            } else {
                return redirect()->route('surat-masuk.index')->with('success', 'Surat masuk berhasil disimpan.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
            } else {
                return back()->withErrors($e->errors())->withInput();
            }
        } catch (\Exception $e) {
            $errorMessage = 'Terjadi kesalahan saat menyimpan surat masuk: ' . $e->getMessage();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            } else {
                return back()->with('error', $errorMessage);
            }
        }
    }


public function update(Request $request, $id)
    {
        try {
            $surat = SuratMasuk::findOrFail($id);

            $validated = $request->validate([
                'nomor_surat' => 'required|string|max:255',
                'divisi_id' => 'required|exists:divisi,id',
                'tanggal' => 'required|date_format:Y-m-d',
                'pengirim' => 'required|string|max:255',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
                'instruksi_disposisi' => 'nullable|string',
                'instruksi_tambahan' => 'nullable|string',
            ], [
                'divisi_id.required' => 'Divisi harus dipilih.',
                'divisi_id.exists' => 'Divisi yang dipilih tidak valid.',
            ]);

            // Log untuk debug
            Log::info('Update request data:', $validated);

            // Siapkan data untuk diupdate
            $updateData = $validated;

            // Konversi tanggal secara manual sebelum update
            if (isset($updateData['tanggal'])) {
                $updateData['tanggal'] = \Carbon\Carbon::createFromFormat('Y-m-d', $updateData['tanggal']);
            }

            // Dapatkan nama divisi berdasarkan divisi_id
            if (isset($validated['divisi_id'])) {
                $divisi = Divisi::find($validated['divisi_id']);
                if ($divisi) {
                    $updateData['nama_divisi'] = $divisi->nama_divisi;
                }
            }

            // Handle file upload jika ada file baru
            if ($request->hasFile('file')) {
                // Hapus file lama jika ada
                if ($surat->file_path) {
                    Storage::disk('public')->delete($surat->file_path);
                }
                // Simpan file baru
                $file = $request->file('file');
                $path = $file->store('surat_masuk', 'public');
                $updateData['file_path'] = $path;
                $updateData['ukuran'] = $file->getSize();

                // Tentukan format_file_id secara otomatis berdasarkan ekstensi file yang diunggah
                $extension = strtolower($file->getClientOriginalExtension());
                $formatFileId = null;

                switch ($extension) {
                    case 'pdf':
                        $formatFileId = \App\Models\FormatFile::where('nama_format', 'PDF')->value('id');
                        break;
                    case 'doc':
                    case 'docx':
                        $formatFileId = \App\Models\FormatFile::where('nama_format', 'DOCX')->value('id');
                        if (!$formatFileId) $formatFileId = \App\Models\FormatFile::where('nama_format', 'DOC')->value('id'); // Fallback
                        break;
                    case 'xls':
                    case 'xlsx':
                        $formatFileId = \App\Models\FormatFile::where('nama_format', 'XLSX')->value('id');
                        if (!$formatFileId) $formatFileId = \App\Models\FormatFile::where('nama_format', 'XLS')->value('id'); // Fallback
                        break;
                    default:
                        break;
                }
                $updateData['format_file_id'] = $formatFileId;
            } else {
            }

            // Lakukan update massal
            $surat->update($updateData);

            // Response depending on request type
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Surat masuk berhasil diperbarui.']);
            } else {
                return redirect()->route('surat-masuk.index')->with('success', 'Surat masuk berhasil diperbarui.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error on update: ', $e->errors());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput()->with('error', 'Validasi gagal. Periksa kembali data yang Anda masukkan.');
        } catch (\Exception $e) {
            Log::error('Error updating surat masuk: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMessage = 'Terjadi kesalahan saat memperbarui surat masuk.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }
            return back()->with('error', $errorMessage);
        }
    }

    public function show($id)
    {
        $surat = SuratMasuk::with(['divisi', 'formatFile'])->findOrFail($id);

        $data = [
            'success' => true,
            'surat' => [
                'id' => $surat->id,
                'nomor_surat' => $surat->nomor_surat,
                'tanggal' => $surat->tanggal ? $surat->tanggal->format('d/m/Y') : '-',
                'pengirim' => $surat->pengirim,
                'perihal' => $surat->perihal,
                'divisi_name' => $surat->divisi->nama_divisi ?? $surat->nama_divisi ?? 'N/A',
                'status' => $surat->status,
                'status_name' => ucfirst($surat->status),
                'format_file_id' => $surat->format_file_id,
                'file_path' => $surat->file_path,
            ]
        ];

        // Jika permintaan datang dari AJAX/Fetch (JavaScript), kembalikan JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($data);
        }

        // Jika diakses langsung lewat URL browser, arahkan kembali ke dashboard
        return redirect()->route('dashboard')->with('error', 'Silakan gunakan tombol lihat di dalam tabel.');
    }

    // ✅ tambahan baru
    public function detail($id)
    {
        $surat = SuratMasuk::findOrFail($id);
        return view('surat_masuk.show', compact('surat'));
    }

    public function belumDitindak()
    {
        $suratMasuk = SuratMasuk::with('divisi')->where('status', 'baru')->get();
        return view('surat_masuk.belum-ditindak', compact('suratMasuk'));
    }

    public function dashboard()
    {
        // Variabel untuk dashboard
        $suratMasukCount = SuratMasuk::count();
        $suratKeluarCount = 0; // Placeholder, jika ada model SuratKeluar bisa dihitung
        $belumDitindakCount = SuratMasuk::where('status', 'baru')->count();
        $belumDitindakSidebarCount = $belumDitindakCount;
        $avgWaktuCount = 0; // Placeholder, hitung rata-rata waktu proses
        $totalLaporanCount = 0; // Placeholder
        $totalArsipCount = 0; // Placeholder
        $totalUsersCount = 0; // Placeholder
        $efisiensiCount = 0; // Placeholder
        $suratMasukHariIni = SuratMasuk::whereDate('created_at', today())->count();
        $suratKeluarHariIni = 0; // Placeholder

        // Ambil data divisi untuk view agar tidak error undefined variable: divisi
        $divisi = \App\Models\Divisi::select('id', 'nama_divisi')->get();

        // Hitung jumlah arsip berdasarkan format file
        $pdfCount = \App\Models\Arsip::whereHas('formatFile', function ($query) { $query->where('nama_format', 'PDF'); })->count();
        $wordCount = \App\Models\Arsip::whereHas('formatFile', function ($query) { $query->where('nama_format', 'DOCX')->orWhere('nama_format', 'DOC'); })->count();
        $excelCount = \App\Models\Arsip::whereHas('formatFile', function ($query) { $query->where('nama_format', 'XLSX')->orWhere('nama_format', 'XLS'); })->count();

        // Data untuk tabel dan chart
        $suratMasuk = SuratMasuk::with('divisi')->orderBy('tanggal', 'desc')->get();
        $suratKeluar = collect(); // Placeholder

        // Tidak perlu format tanggal manual lagi, akan ditangani di view
        $workloadData = collect(); // Placeholder
        $arsip = collect(); // Placeholder
        $laporan = collect(); // Placeholder
        $users = collect(); // Placeholder

        return view('dashboard', compact(
            'suratMasukCount', 'suratKeluarCount', 'belumDitindakCount', 'belumDitindakSidebarCount', 'avgWaktuCount',
            'totalLaporanCount', 'totalArsipCount', 'totalUsersCount', 'efisiensiCount',
            'suratMasukHariIni', 'suratKeluarHariIni', 'suratMasuk', 'suratKeluar', 'pdfCount', 'wordCount', 'excelCount',
            'workloadData', 'arsip', 'laporan', 'users', 'divisi'
        ));
    }

    public function destroy(Request $request, $id)
    {
        try {
            $surat = SuratMasuk::findOrFail($id);

            if ($surat->file_path) {
                Storage::disk('public')->delete($surat->file_path);
            }

            $surat->delete();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Surat masuk berhasil dihapus.']);
            } else {
                return redirect()->route('surat-masuk.index')->with('success', 'Surat masuk berhasil dihapus.');
            }
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            } else {
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            // Pastikan hanya admin yang bisa mengubah status
            if (Auth::user()->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini.'], 403);
            }

            include_once app_path('Helpers/functions.php');

            $validated = $request->validate([
                'status' => 'required|in:baru,diterima,ditolak,diproses,selesai,draft,dikirim',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $surat = SuratMasuk::findOrFail($id);
            $surat->status = $validated['status'];
            $surat->save();

            RiwayatStatus::create([
                'arsip_id' => null,
                'surat_masuk_id' => $surat->id,
                'status' => $validated['status'],
                'user_id' => Auth::id(),
                'keterangan' => $validated['keterangan'] ?? null,
                'tanggal' => now(),
            ]);

            // broadcast(new SuratMasukUpdated($surat, 'updated'))->toOthers();

            return response()->json([
                'success' => true,
                'new_status' => $validated['status'],
                'status_name' => getStatusName($validated['status']),
                'message' => 'Status berhasil diubah.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function welcome(Request $request)
    {
        Log::info("Request received: " . $request->method() . " " . $request->path());
        return response()->json(['message' => 'Welcome to the Surat Masuk API!']);
    }
}
