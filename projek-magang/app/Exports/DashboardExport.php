<?php

namespace App\Exports;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Divisi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class DashboardExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new DashboardSummarySheet($this->filters),
            new SuratMasukSheet($this->filters),
            new SuratKeluarSheet($this->filters),
            new DivisiPerformanceSheet($this->filters),
        ];
    }
}

class DashboardSummarySheet implements FromCollection, WithHeadings, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Ringkasan Dashboard';
    }

    public function collection()
    {
        // Get summary data
        $suratMasukCount = SuratMasuk::count();
        $suratKeluarCount = SuratKeluar::count();
        $belumDitindakCount = SuratMasuk::where('status', 'baru')->count();
        $totalUsers = \App\Models\User::count();

        return collect([
            ['Metrik', 'Nilai'],
            ['Total Surat Masuk', $suratMasukCount],
            ['Total Surat Keluar', $suratKeluarCount],
            ['Belum Ditindak', $belumDitindakCount],
            ['Total Pengguna', $totalUsers],
            ['Efisiensi Sistem', '95%'], // You can calculate this based on your logic
        ]);
    }

    public function headings(): array
    {
        return ['Metrik', 'Nilai'];
    }
}

class SuratMasukSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Surat Masuk';
    }

    public function collection()
    {
        $query = SuratMasuk::with(['divisi', 'formatFile']);

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('tanggal', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        return $query->get()->map(function ($surat) {
            return [
                $surat->id,
                $surat->nomor_surat,
                $surat->tanggal->format('d/m/Y'),
                $surat->pengirim,
                $surat->perihal,
                $surat->divisi->name ?? '',
                ucfirst($surat->status),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Nomor Surat', 'Tanggal', 'Pengirim', 'Perihal', 'Divisi', 'Status'];
    }
}

class SuratKeluarSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Surat Keluar';
    }

    public function collection()
    {
        $query = SuratKeluar::with(['divisi', 'user']);

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('tanggal_kirim', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        return $query->get()->map(function ($surat) {
            return [
                $surat->id,
                $surat->nomor_surat,
                $surat->tanggal_kirim->format('d/m/Y'),
                $surat->penerima,
                $surat->perihal,
                $surat->divisi->name ?? '',
                ucfirst($surat->status),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Nomor Surat', 'Tanggal Kirim', 'Penerima', 'Perihal', 'Divisi', 'Status'];
    }
}

class DivisiPerformanceSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Kinerja Divisi';
    }

    public function collection()
    {
        return Divisi::with(['suratMasuk', 'suratKeluar'])->get()->map(function ($divisi) {
            $suratMasuk = $divisi->suratMasuk->count();
            $suratKeluar = $divisi->suratKeluar->count();
            $belumDitindak = $divisi->suratMasuk->where('status', 'baru')->count();

            return [
                $divisi->name,
                $suratMasuk,
                $suratKeluar,
                $belumDitindak,
                $suratMasuk + $suratKeluar,
            ];
        });
    }

    public function headings(): array
    {
        return ['Divisi', 'Surat Masuk', 'Surat Keluar', 'Belum Ditindak', 'Total'];
    }
}
