<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuratMasuk>
 */
class SuratMasukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nomor_surat' => $this->faker->unique()->word(),
            'judul_laporan' => $this->faker->sentence(),
            'divisi_id' => \App\Models\Divisi::factory(),
            'tanggal' => $this->faker->date(),
            'perihal' => $this->faker->paragraph(),
            'pengirim' => $this->faker->name(),
            'format_file_id' => \App\Models\FormatFile::factory(),
            'status' => 'baru',
            'file_path' => null,
            'ukuran' => null,
        ];
    }
}
