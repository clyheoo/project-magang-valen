<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Divisi>
 */
class DivisiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nama_divisi' => $this->faker->company(),
            'name' => $this->faker->company(),
            'code' => $this->faker->unique()->word(),
            'deskripsi' => $this->faker->sentence(),
            'surat_masuk_count' => 0,
            'surat_keluar_count' => 0,
            'belum_ditindak_count' => 0,
        ];
    }
}
