<?php

namespace App\Exports;

use App\Models\SuratKeluar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuratKeluarExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = SuratKeluar::with(['divisi', 'user']);

        // Apply filters
        if (!empty($this->filters['divisi'])) {
            $query->where('divisi_id', $this->filters['divisi']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('tanggal_kirim', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Surat',
            'Tanggal Kirim',
            'Penerima',
            'Perihal',
            'Divisi',
            'Status',
            'Dibuat Oleh'
        ];
    }

    public function map($surat): array
    {
        return [
            $surat->id,
            $surat->nomor_surat,
            $surat->tanggal_kirim->format('d/m/Y'),
            $surat->penerima,
            $surat->perihal,
            $surat->divisi->name ?? '',
            ucfirst($surat->status),
            $surat->user->name ?? ''
        ];
    }
}
