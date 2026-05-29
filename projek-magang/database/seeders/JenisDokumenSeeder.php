<?php

namespace Database\Seeders;
 
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JenisDokumen;

class JenisDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menonaktifkan foreign key checks sementara
        Schema::disableForeignKeyConstraints();

        // Hapus data lama untuk menghindari duplikasi jika seeder dijalankan lagi
        JenisDokumen::truncate();

        $jenisDokumen = [
            ['nama_jenis' => 'Surat Keputusan'],
            ['nama_jenis' => 'Surat Edaran'],
            ['nama_jenis' => 'Memo Internal'],
            ['nama_jenis' => 'Nota Dinas'],
            ['nama_jenis' => 'Laporan Bulanan'],
            ['nama_jenis' => 'Dokumen Proyek'],
            ['nama_jenis' => 'Lainnya'],
        ];

        // Masukkan data ke tabel jenis_dokumen
        foreach ($jenisDokumen as $dokumen) {
            JenisDokumen::create($dokumen);
        }

        // Mengaktifkan kembali foreign key checks
        Schema::enableForeignKeyConstraints();
    }
}
