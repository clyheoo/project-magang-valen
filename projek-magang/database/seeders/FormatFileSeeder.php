<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FormatFile;

class FormatFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $formatFiles = [
            ['nama_format' => 'PDF'],
            ['nama_format' => 'DOC'],
            ['nama_format' => 'DOCX'],
            ['nama_format' => 'XLS'],
            ['nama_format' => 'XLSX'],
        ];

        foreach ($formatFiles as $format) {
            FormatFile::updateOrCreate(
                ['nama_format' => $format['nama_format']],
                $format
            );
        }
    }
}
