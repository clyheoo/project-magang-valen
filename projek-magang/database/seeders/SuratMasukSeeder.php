<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuratMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suratMasuk = [
            [
                'nomor_surat' => '001/SM/2025',
                'judul_laporan' => 'Permohonan Kerjasama PT. ABC',
                'tanggal' => now()->subDays(1),
                'pengirim' => 'PT. ABC',
                'perihal' => 'Permohonan Kerjasama',
                'divisi_code' => 'umum',
                'status' => 'baru',
                'format_file_id' => 1,
            ],
            [
                'nomor_surat' => '002/SM/2025',
                'judul_laporan' => 'Undangan Seminar Universitas XYZ',
                'tanggal' => now()->subDays(2),
                'pengirim' => 'Universitas XYZ',
                'perihal' => 'Undangan Seminar',
                'divisi_code' => 'akademik',
                'status' => 'diterima',
                'format_file_id' => 2,
            ],
            [
                'nomor_surat' => '003/SM/2025',
                'judul_laporan' => 'Laporan Keuangan Dinas Keuangan',
                'tanggal' => now()->subDays(3),
                'pengirim' => 'Dinas Keuangan',
                'perihal' => 'Laporan Keuangan',
                'divisi_code' => 'keuangan',
                'status' => 'diproses',
                'format_file_id' => 3,
            ],
        ];

        foreach ($suratMasuk as $surat) {
            $divisi = \App\Models\Divisi::where('code', $surat['divisi_code'])->first();
            if ($divisi) {
                \App\Models\SuratMasuk::create([
                    'nomor_surat' => $surat['nomor_surat'],
                    'judul_laporan' => $surat['judul_laporan'],
                    'tanggal' => $surat['tanggal'],
                    'pengirim' => $surat['pengirim'],
                    'perihal' => $surat['perihal'],
                    'divisi_id' => $divisi->id,
                    'status' => $surat['status'],
                    'format_file_id' => $surat['format_file_id'],
                ]);
            }
        }
    }
}
