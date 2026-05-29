<?php

namespace App\Exports;

use App\Models\SuratMasuk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuratMasukExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = SuratMasuk::with(['divisi', 'formatFile']);

        // Apply filters
        if (!empty($this->filters['divisi'])) {
            $query->where('divisi_id', $this->filters['divisi']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('tanggal', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Surat',
            'Tanggal',
            'Pengirim',
            'Perihal',
            'Divisi',
            'Status',
            'Format File',
            'Ukuran File (KB)'
        ];
    }

    public function map($surat): array
    {
        return [
            $surat->id,
            $surat->nomor_surat,
            $surat->tanggal->format('d/m/Y'),
            $surat->pengirim,
            $surat->perihal,
            $surat->divisi->name ?? '',
            ucfirst($surat->status),
            $surat->formatFile->name ?? '',
            $surat->ukuran ? number_format($surat->ukuran / 1024, 2) : ''
        ];
    }
}
