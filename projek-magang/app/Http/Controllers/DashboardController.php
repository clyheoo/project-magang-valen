<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Arsip;
use App\Models\Laporan;
use App\Models\User;
use App\Models\Divisi;
use App\Models\RiwayatStatus;
use App\Exports\SuratMasukExport;
use App\Exports\SuratKeluarExport;
use App\Exports\DashboardExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Events\SuratMasukUpdated;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $divisiId = $user->divisi_id;

        // --- PENGAMBILAN DATA UTAMA ---
        $suratMasukQuery = SuratMasuk::query();
        $suratKeluarQuery = SuratKeluar::query();

        if ($user->role !== 'admin') {
            $suratMasukQuery->where('created_by', $user->id);
            $suratKeluarQuery->where('created_by', $user->id);
        }

        // Clone untuk perhitungan count
        $suratMasukCount = (clone $suratMasukQuery)->count();
        $suratKeluarCount = (clone $suratKeluarQuery)->count();
        $belumDitindakCount = (clone $suratMasukQuery)->where('status', 'baru')->count();

        // Data sidebar
        if ($user->role === 'admin') {
            $suratMasukHariIni = SuratMasuk::whereDate('tanggal', today())->count();
            $suratKeluarHariIni = SuratKeluar::whereDate('tanggal_kirim', today())->count();
            $belumDitindakSidebarCount = SuratMasuk::where('status', 'baru')->count();
        } else {
            $suratMasukHariIni = (clone $suratMasukQuery)->whereDate('tanggal', today())->count();
            $suratKeluarHariIni = (clone $suratKeluarQuery)->whereDate('tanggal_kirim', today())->count();
            $belumDitindakSidebarCount = $belumDitindakCount;
        }

        // Rata-rata waktu
        $avgWaktuCount = round(SuratMasuk::where('status', 'baru')->avg(\DB::raw('DATEDIFF(NOW(), tanggal)')), 1) ?: 0;

        // Data tabel
        $suratMasuk = $suratMasukQuery->with(['divisi', 'formatFile'])->latest()->get();
        $suratKeluar = $suratKeluarQuery->with(['divisi', 'user', 'formatFile'])->latest('tanggal_kirim')->paginate(10);
        $divisi = Divisi::all();

        // --- DATA KHUSUS ADMIN ---
        if ($user->role === 'admin') {
            $totalLaporanCount = Laporan::count();
            $totalUsersCount = User::count();
            $efisiensiCount = $suratMasukCount > 0 ? round(((clone $suratMasukQuery)->where('status', '!=', 'baru')->count() / $suratMasukCount) * 100) : 0;

            // Hitung format files
            $pdfCount = SuratMasuk::where('format_file_id', 1)->count() + SuratKeluar::where('format_file_id', 1)->count() + Arsip::where('format_file_id', 1)->count();
            $wordCount = SuratMasuk::where('format_file_id', 2)->count() + SuratKeluar::where('format_file_id', 2)->count() + Arsip::where('format_file_id', 2)->count();
            $excelCount = SuratMasuk::where('format_file_id', 3)->count() + SuratKeluar::where('format_file_id', 3)->count() + Arsip::where('format_file_id', 3)->count();
            $totalArsipCount = $pdfCount + $wordCount + $excelCount;

            // Ringkasan sistem
            $totalDocuments = $suratMasukCount + $suratKeluarCount + $totalArsipCount;
            $activeUsers = User::where('status', 'aktif')->count();
            $processedToday = RiwayatStatus::whereIn('status', ['diproses', 'selesai'])->whereDate('tanggal', today())->distinct('surat_masuk_id')->count();
            $pendingItems = $belumDitindakCount;

            // Data laporan section
            $totalSuratMasuk = $suratMasukCount;
            $totalSuratKeluar = $suratKeluarCount;
            $efisiensiProses = $efisiensiCount;
            $avgProcessingTime = $avgWaktuCount . ' hari';
            $onTimePercentage = $efisiensiCount;

            $mostActiveDivisionData = Divisi::withCount(['suratMasuk', 'suratKeluar'])->get()->sortByDesc(fn($d) => $d->surat_masuk_count + $d->surat_keluar_count)->first();
            $mostActiveDivision = $mostActiveDivisionData?->nama_divisi ?? 'N/A';
            $totalFilesProcessed = $totalDocuments;

            // Arsip gabungan
            $arsipSuratMasuk = SuratMasuk::with('formatFile')->get()->map(fn($item) => (object)[
                'id' => $item->id, 'nomor_surat' => $item->nomor_surat, 'tanggal_raw' => $item->tanggal,
                'tanggal' => $item->tanggal?->format('d/m/Y') ?? '', 'perihal' => $item->perihal,
                'keterangan' => 'Pengirim: ' . $item->pengirim, 'jenis' => 'Surat Masuk', 'jenis_color' => 'info',
                'format_id' => $item->format_file_id, 'format' => $item->formatFile?->nama_format,
                'ukuran' => $this->formatSizeUnits($item->ukuran), 'file_path' => $item->file_path,
            ]);

            $arsipSuratKeluar = SuratKeluar::with('formatFile')->get()->map(fn($item) => (object)[
                'id' => $item->id, 'nomor_surat' => $item->nomor_surat, 'tanggal_raw' => $item->tanggal_kirim,
                'tanggal' => $item->tanggal_kirim?->format('d/m/Y') ?? '', 'perihal' => $item->perihal,
                'keterangan' => 'Penerima: ' . $item->penerima, 'jenis' => 'Surat Keluar', 'jenis_color' => 'success',
                'format_id' => $item->format_file_id, 'format' => $item->formatFile?->nama_format,
                'ukuran' => $this->formatSizeUnits($item->ukuran), 'file_path' => $item->file_path,
            ]);

            $arsipLain = Arsip::with(['formatFile', 'jenisSurat'])->get()->map(fn($item) => (object)[
                'id' => 'arsip-' . $item->id, 'nomor_surat' => $item->nomor_surat, 'tanggal_raw' => $item->tanggal,
                'tanggal' => $item->tanggal?->format('d/m/Y') ?? '', 'perihal' => $item->perihal,
                'keterangan' => $item->keterangan, 'jenis' => $item->jenisSurat?->nama_jenis ?? 'Lainnya', 'jenis_color' => 'secondary',
                'format_id' => $item->format_file_id, 'format' => $item->formatFile?->nama_format,
                'ukuran' => $this->formatSizeUnits($item->ukuran), 'file_path' => $item->file_path,
            ]);

            $arsip = $arsipSuratMasuk->concat($arsipSuratKeluar)->concat($arsipLain)->sortByDesc('tanggal_raw')->values();

            // Users
            $users = User::with('divisi')->get();

            // Workload data
            $divisis = Divisi::withCount(['suratMasuk', 'suratKeluar', 'suratMasuk as belum_ditindak_count' => fn($q) => $q->whereIn('status', ['baru', 'diproses'])])->get();
            $workloadData = [];
            $workloadStatusCounts = ['Baik' => 0, 'Cukup' => 0, 'Perlu Perhatian' => 0];

            foreach ($divisis as $division) {
                $totalSurat = $division->surat_masuk_count + $division->surat_keluar_count;
                $statusName = $division->belum_ditindak_count <= 2 ? 'Baik' : ($division->belum_ditindak_count <= 5 ? 'Cukup' : 'Perlu Perhatian');
                $statusColor = match($statusName) { 'Baik' => 'success', 'Cukup' => 'warning', default => 'danger' };
                $workloadStatusCounts[$statusName]++;
                $workloadData[] = (object)[
                    'divisi_code' => $division->code, 'divisi_name' => $division->nama_divisi,
                    'total_surat' => $totalSurat, 'surat_masuk' => $division->surat_masuk_count,
                    'surat_keluar' => $division->surat_keluar_count, 'belum_ditindak' => $division->belum_ditindak_count,
                    'avg_waktu' => 2.0, 'status_color' => $statusColor, 'status_name' => $statusName,
                ];
            }

            // Monthly trend
            $monthlyTrend = ['labels' => [], 'surat_masuk' => [], 'surat_keluar' => []];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthlyTrend['labels'][] = $date->format('M Y');
                $monthlyTrend['surat_masuk'][] = SuratMasuk::whereYear('tanggal', $date->year)->whereMonth('tanggal', $date->month)->count();
                $monthlyTrend['surat_keluar'][] = SuratKeluar::whereYear('tanggal_kirim', $date->year)->whereMonth('tanggal_kirim', $date->month)->count();
            }

            // Division distribution
            $divisionDistribution = Divisi::withCount(['suratMasuk', 'suratKeluar'])->get()->map(fn($d) => (object)[
                'name' => $d->nama_divisi, 'total' => $d->surat_masuk_count + $d->surat_keluar_count,
            ])->filter(fn($item) => $item->total > 0)->values();

            $totalLaporan = Laporan::count();

            // Recent activities
            $recentMasuk = SuratMasuk::with('user')->latest('updated_at')->take(5)->get()->map(fn($item) => (object)[
                'type' => 'Surat Masuk', 'icon' => 'fa-envelope', 'color' => 'primary',
                'title' => $item->perihal, 'user' => $item->user->name ?? 'Sistem', 'date' => $item->updated_at,
            ]);
            $recentKeluar = SuratKeluar::with('user')->latest('updated_at')->take(5)->get()->map(fn($item) => (object)[
                'type' => 'Surat Keluar', 'icon' => 'fa-paper-plane', 'color' => 'success',
                'title' => $item->perihal, 'user' => $item->user->name ?? 'Sistem', 'date' => $item->updated_at,
            ]);
            $recentActivities = $recentMasuk->concat($recentKeluar)->sortByDesc('date')->take(5);

            // Data untuk Chart Dashboard Utama
            $divisionChartData = Divisi::withCount(['suratMasuk', 'suratKeluar'])->get()->map(fn($d) => [
                'name' => $d->nama_divisi,
                'surat_masuk' => $d->surat_masuk_count,
                'surat_keluar' => $d->surat_keluar_count
            ]);

            $statusChartData = SuratMasuk::select('status', \DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            // Return view ADMIN
            return view('dashboard.admin', compact(
                'recentActivities', 'suratMasukHariIni', 'suratKeluarHariIni', 'belumDitindakSidebarCount',
                'suratMasukCount', 'suratKeluarCount', 'avgWaktuCount', 'totalLaporanCount', 'totalArsipCount',
                'totalUsersCount', 'efisiensiCount', 'totalDocuments', 'activeUsers', 'processedToday', 'pendingItems',
                'belumDitindakCount', 'totalSuratMasuk', 'totalSuratKeluar', 'pdfCount', 'wordCount', 'excelCount',
                'suratMasuk', 'suratKeluar', 'arsip', 'users', 'workloadData', 'workloadStatusCounts',
                'efisiensiProses', 'avgProcessingTime', 'onTimePercentage', 'mostActiveDivision', 'totalFilesProcessed',
                'divisi', 'monthlyTrend', 'divisionDistribution', 'totalLaporan', 'divisionChartData','statusChartData' 
            ));
        }

        // Return view USER
        return view('dashboard.user', compact(
            'suratMasukCount', 'suratKeluarCount', 'belumDitindakCount', 'suratMasuk', 'suratKeluar',
            'divisi', 'suratMasukHariIni', 'suratKeluarHariIni', 'belumDitindakSidebarCount', 'avgWaktuCount'
        ));
    }

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
     * Mendapatkan warna untuk format file
     *
     * @param string $format
     * @return string
     */
    private function getFormatColor($format)
    {
        $colors = [
            'PDF' => 'danger',
            'DOCX' => 'primary',
            'XLSX' => 'success',
            'XLS' => 'success',
        ];

        return $colors[$format] ?? 'secondary';
    }

    /**
     * Format ukuran file dari bytes ke unit yang lebih mudah dibaca.
     *
     * @param int $bytes
     * @return string
     */
    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1048576) { // MB
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }

    /**
     *
     * @param string $code
     * @return string
     */
    private function getDepartmentName($code) // Fungsi ini tidak digunakan di controller ini, tapi kita biarkan saja
    {
        $names = [
            'umum' => 'Umum & Kepegawaian',
            'keuangan' => 'Keuangan',
            'perencanaan' => 'Perencanaan',
            'hukum' => 'Hukum & Kerjasama',
            'ti' => 'Teknologi Informasi',
        ];

        return $names[$code] ?? $code;
    }

    /**
     * Menampilkan detail surat masuk
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showSuratMasuk($id)
    {
        $surat = SuratMasuk::with(['divisi', 'riwayatStatus' => function($query) {
            $query->orderBy('tanggal', 'desc');
        }])->findOrFail($id);

        return view('surat_masuk.show', compact('surat'));
    }

    /**
     * Update status surat masuk
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:surat_masuk,id',
            'status' => 'required|string|in:baru,diterima,ditolak,diproses,selesai',
        ]);

        $surat = SuratMasuk::findOrFail($request->id);
        $oldStatus = $surat->status;
        $surat->status = $request->status;
        $surat->save();

        // Simpan riwayat status
        RiwayatStatus::create([
            'surat_masuk_id' => $surat->id,
            'status' => $request->status,
            'user_id' => auth()->id(),
            'tanggal' => now(),
            'keterangan' => 'Status diperbarui menjadi ' . $this->getStatusName($request->status),
        ]);

        // Broadcast event untuk real-time update
        broadcast(new SuratMasukUpdated($surat, 'updated'));

        return response()->json([
            'success' => true,
            'old_status' => $oldStatus,
        ]);
    }

    /**
     * Menampilkan data untuk grafik distribusi surat per divisi
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDivisionChartData()
    {
        $user = auth()->user();
        $divisions = Divisi::all();
        $data = [];

        foreach ($divisions as $division) {
            $suratMasukQuery = SuratMasuk::where('divisi_id', $division->id);
            $suratKeluarQuery = SuratKeluar::where('divisi_id', $division->id);

            if ($user->role !== 'admin') {
                // For non-admin users, show data for their division only
                if ($user->divisi_id !== $division->id) {
                    $data[] = [
                        'name' => $division->nama_divisi,
                        'surat_masuk' => 0,
                        'surat_keluar' => 0,
                    ];
                    continue;
                }
            }

            $data[] = [
                'name' => $division->nama_divisi,
                'surat_masuk' => $suratMasukQuery->count(),
                'surat_keluar' => $suratKeluarQuery->count(),
            ];
        }

        return response()->json($data);
    }

    /**
     * Menampilkan data untuk grafik status surat masuk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatusChartData()
    {
        $user = auth()->user();
        $query = SuratMasuk::select('status', \DB::raw('count(*) as total'));

        if ($user->role !== 'admin') {
            $query->where('created_by', $user->id);
        }

        $statusData = $query->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        return response()->json($statusData);
    }

    /**
     * Mendapatkan jumlah surat yang belum ditindak
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBelumDitindakCount()
    {
        $count = SuratMasuk::where('status', 'baru')->count();

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Export dashboard data to Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'divisi', 'status']);

        return Excel::download(new DashboardExport($filters), 'dashboard-report-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export dashboard data to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'divisi', 'status']);

        // Get data for PDF
        $data = $this->getDashboardData($filters);

        $pdf = Pdf::loadView('exports.dashboard-pdf', $data);

        return $pdf->download('dashboard-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function getRealtimeSystemStatus()
    {
        try {
            $user = auth()->user();

            if ($user->role === 'admin') {
                $suratMasukHariIni = SuratMasuk::whereDate('tanggal', today())->count();
                $suratKeluarHariIni = SuratKeluar::whereDate('tanggal_kirim', today())->count();
                $belumDitindakCount = SuratMasuk::where('status', 'baru')->count();
            } else {
                $suratMasukHariIni = SuratMasuk::where('created_by', $user->id)->whereDate('tanggal', today())->count();
                $suratKeluarHariIni = SuratKeluar::where('created_by', $user->id)->whereDate('tanggal_kirim', today())->count();
                $belumDitindakCount = SuratMasuk::where('created_by', $user->id)->where('status', 'baru')->count();
            }

            return response()->json([
                'suratMasukHariIni' => $suratMasukHariIni,
                'suratKeluarHariIni' => $suratKeluarHariIni,
                'belumDitindak' => $belumDitindakCount,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true], 200); // Return 200 agar tidak error looping di JS
        }
    }

    /**
     * Menampilkan daftar surat masuk yang belum ditindak (status 'baru')
     *
     * @return \Illuminate\View\View
     */
    public function belumDitindak()
    {
        $suratMasuk = SuratMasuk::with(['divisi', 'riwayatStatus' => function($query) {
            $query->orderBy('tanggal', 'desc');
        }])->where('status', 'baru')->latest()->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'nomor_surat' => $item->nomor_surat,
                'tanggal' => $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '',
                'pengirim' => $item->pengirim,
                'perihal' => $item->perihal,
                'divisi_code' => $item->divisi->code ?? ($item->divisi_id ?? null),
                'divisi_name' => $item->divisi->nama_divisi ?? ($item->nama_divisi ?? 'N/A'),
                'status_color' => $this->getStatusColor($item->status),
                'status_name' => $this->getStatusName($item->status),
                'file_path' => $item->file_path,
                'ukuran' => $item->ukuran,
                'riwayat_count' => $item->riwayatStatus->count(),
            ];
        });

        $totalBelumDitindak = $suratMasuk->count();

        return view('belum-ditindak', compact('suratMasuk', 'totalBelumDitindak'));
    }

    /**
     * Menampilkan detail surat belum ditindak via API
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showBelumDitindak($id)
    {
        try {
            $surat = SuratMasuk::with(['divisi', 'riwayatStatus' => function($query) {
                $query->orderBy('tanggal', 'desc');
            }])->where('status', 'baru')->findOrFail($id);

            // Hitung lama hari
            $tanggalTerima = \Carbon\Carbon::parse($surat->tanggal);
            $hariIni = \Carbon\Carbon::now();
            $lamaHari = $tanggalTerima->diffInDays($hariIni);

            // Tentukan urgensi berdasarkan lama hari
            if ($lamaHari >= 7) {
                $urgensi = 'tinggi';
            } elseif ($lamaHari >= 3) {
                $urgensi = 'sedang';
            } else {
                $urgensi = 'rendah';
            }

            return response()->json([
                'success' => true,
                'surat' => [
                    'id' => $surat->id,
                    'nomor_surat' => $surat->nomor_surat,
                    'pengirim' => $surat->pengirim,
                    'tanggal' => $surat->tanggal->format('d/m/Y'),
                    'perihal' => $surat->perihal,
                    'divisi_name' => $surat->divisi->nama_divisi ?? ($surat->nama_divisi ?? 'N/A'),
                    'file_path' => $surat->file_path,
                    'lama_hari' => $lamaHari,
                    'urgensi' => $urgensi,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Surat tidak ditemukan atau sudah ditindak.'
            ], 404);
        }
    }

    /**
     * Mengirim reminder untuk surat belum ditindak
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendReminder($id)
    {
        try {
            $surat = SuratMasuk::where('status', 'baru')->findOrFail($id);

            // Simpan riwayat reminder
            RiwayatStatus::create([
                'surat_masuk_id' => $surat->id,
                'status' => 'baru', // Tetap baru karena belum ditindak
                'user_id' => auth()->id(),
                'tanggal' => now(),
                'keterangan' => 'Reminder dikirim untuk surat yang belum ditindak',
            ]);

            // TODO: Implementasi pengiriman email reminder jika diperlukan
            // Mail::to($surat->divisi->email ?? 'admin@example.com')->send(new ReminderSurat($surat));

            return response()->json([
                'success' => true,
                'message' => 'Reminder berhasil dikirim.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menandai surat sebagai sudah ditindak
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tindakSurat(Request $request)
    {
        $request->validate([
            'surat_id' => 'required|integer|exists:surat_masuk,id',
            'tanggal_tindakan' => 'required|date_format:Y-m-d',
            'deskripsi' => 'required|string|max:255',
            'status_akhir' => 'required|in:selesai,ditunda,diteruskan',
            'catatan' => 'nullable|string|max:500',
        ]);

        // Parse the date with the correct format to avoid parsing errors.
        $tanggalTindakan = \Carbon\Carbon::createFromFormat('Y-m-d', $request->tanggal_tindakan);

        try {
            $surat = SuratMasuk::findOrFail($request->surat_id);

            // Pastikan surat masih berstatus 'baru'
            if ($surat->status !== 'baru') {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat sudah ditindak sebelumnya.'
                ], 400);
            }

            // Update status surat berdasarkan status akhir
            $statusMapping = [
                'selesai' => 'selesai',
                'ditunda' => 'diproses',
                'diteruskan' => 'diproses',
            ];

            $surat->status = $statusMapping[$request->status_akhir];
            $surat->save();

            // Simpan riwayat tindakan
            RiwayatStatus::create([
                'surat_masuk_id' => $surat->id,
                'status' => $surat->status,
                'user_id' => auth()->id(),
                'tanggal' => $tanggalTindakan,
                'keterangan' => 'Surat ditindak: ' . $request->deskripsi .
                               ($request->catatan ? ' - Catatan: ' . $request->catatan : ''),
            ]);

            // Broadcast event untuk real-time update
            broadcast(new SuratMasukUpdated($surat, 'updated'));

            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil ditandai sebagai sudah ditindak.',
                'status_baru' => $surat->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menindak surat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard data for export
     *
     * @param array $filters
     * @return array
     */
    private function getDashboardData($filters = [])
    {
        // Apply filters to queries
        $suratMasukQuery = SuratMasuk::with(['divisi', 'formatFile']);
        $suratKeluarQuery = SuratKeluar::with(['divisi', 'user']);
        $arsipQuery = Arsip::with(['formatFile']);

        if (!empty($filters['divisi'])) {
            $suratMasukQuery->where('divisi_id', $filters['divisi']);
            $suratKeluarQuery->where('divisi_id', $filters['divisi']);
            // For arsip, we might need to filter by divisi if applicable
        }

        if (!empty($filters['status'])) {
            $suratMasukQuery->where('status', $filters['status']);
            $suratKeluarQuery->where('status', $filters['status']);
            // For arsip, status might be different
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $suratMasukQuery->whereBetween('tanggal', [$filters['start_date'], $filters['end_date']]);
            $suratKeluarQuery->whereBetween('tanggal_kirim', [$filters['start_date'], $filters['end_date']]);
            $arsipQuery->whereBetween('tanggal', [$filters['start_date'], $filters['end_date']]);
        }

        $suratMasuk = $suratMasukQuery->get();
        $suratKeluar = $suratKeluarQuery->get();
        $arsip = $arsipQuery->get();

        // Summary data
        $summary = [
            'total_surat_masuk' => $suratMasuk->count(),
            'total_surat_keluar' => $suratKeluar->count(),
            'total_arsip' => $arsip->count(),
            'belum_ditindak' => $suratMasuk->where('status', 'baru')->count(),
            'total_users' => User::count(),
            'total_divisi' => Divisi::count(),
        ];

        return [
            'summary' => $summary,
            'suratMasuk' => $suratMasuk,
            'suratKeluar' => $suratKeluar,
            'arsip' => $arsip,
            'filters' => $filters,
            'generated_at' => now()->format('d/m/Y H:i:s'),
        ];
    }

    /**
     * Stub method to fix missing getPerformanceSummary error.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPerformanceSummary()
    {
        // Returning dummy data for now. Adjust as needed.
        return response()->json([
            'success' => true,
            'message' => 'Performance summary data placeholder',
            'data' => [],
        ]);
    }
}
