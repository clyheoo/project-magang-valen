<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Divisi;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisi = [
            [
                'nama_divisi' => 'KASUBAG TU',
                'name' => 'KASUBAG TU',
                'code' => 'kasubag_tu',
                'deskripsi' => 'Divisi Kasubag Tata Usaha'
            ],
            [
                'nama_divisi' => 'SMA',
                'name' => 'SMA',
                'code' => 'sma',
                'deskripsi' => 'Divisi Sekolah Menengah Atas'
            ],
            [
                'nama_divisi' => 'SMK',
                'name' => 'SMK',
                'code' => 'smk',
                'deskripsi' => 'Divisi Sekolah Menengah Kejuruan'
            ],
        ];

        foreach ($divisi as $item) {
            Divisi::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
