<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\SuratMasuk;
use App\Models\Divisi;
use App\Models\FormatFile;
use App\Models\User;

class SuratMasukTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed necessary data
        $this->seed([
            \Database\Seeders\DivisiSeeder::class,
            \Database\Seeders\FormatFileSeeder::class,
            \Database\Seeders\AdminUserSeeder::class,
        ]);
    }

    /** @test */
    public function it_can_store_surat_masuk_with_valid_divisi()
    {
        $user = User::first();
        $this->actingAs($user);

        $divisi = Divisi::first();
        $formatFile = FormatFile::first();

        // Create a fake file
        $file = \Illuminate\Http\UploadedFile::fake()->create('test.pdf', 1000);

        $data = [
            'nomor_surat' => 'SM/001/2024',
            'judul_laporan' => 'Laporan Test',
            'divisi' => $divisi->id,
            'tanggal' => '2024-01-01',
            'perihal' => 'Perihal Test',
            'pengirim' => 'Pengirim Test',
            'format_file_id' => $formatFile->id,
            'file' => $file,
        ];

        $response = $this->post(route('surat-masuk.store'), $data);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('surat_masuk', [
            'nomor_surat' => 'SM/001/2024',
            'judul_laporan' => 'Laporan Test',
            'divisi_id' => $divisi->id,
        ]);
    }

    /** @test */
    public function it_fails_to_store_surat_masuk_with_invalid_divisi()
    {
        $user = User::first();
        $this->actingAs($user);

        $formatFile = FormatFile::first();

        $data = [
            'nomor_surat' => 'SM/001/2024',
            'judul_laporan' => 'Laporan Test',
            'divisi' => 9999, // Non-existent divisi
            'tanggal' => '2024-01-01',
            'perihal' => 'Perihal Test',
            'pengirim' => 'Pengirim Test',
            'format_file_id' => $formatFile->id,
            'file' => null,
        ];

        $response = $this->post(route('surat-masuk.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors('divisi');
        $this->assertDatabaseMissing('surat_masuk', [
            'nomor_surat' => 'SM/001/2024',
        ]);
    }

    /** @test */
    public function it_can_update_surat_masuk_with_valid_divisi()
    {
        $user = User::first();
        $this->actingAs($user);

        $surat = SuratMasuk::factory()->create();
        $divisi = Divisi::first();
        $formatFile = FormatFile::first();

        $data = [
            'nomor_surat' => 'SM/002/2024',
            'judul_laporan' => 'Updated Laporan',
            'divisi' => $divisi->id,
            'tanggal' => '2024-01-02',
            'perihal' => 'Updated Perihal',
            'pengirim' => 'Updated Pengirim',
            'format_file_id' => $formatFile->id,
        ];

        $response = $this->put(route('surat-masuk.update', $surat->id), $data);

        $response->assertRedirect(route('surat-masuk.index'));
        $this->assertDatabaseHas('surat_masuk', [
            'id' => $surat->id,
            'nomor_surat' => 'SM/002/2024',
            'divisi_id' => $divisi->id,
        ]);
    }

    /** @test */
    public function it_fails_to_update_surat_masuk_with_invalid_divisi()
    {
        $user = User::first();
        $this->actingAs($user);

        $surat = SuratMasuk::factory()->create();
        $formatFile = FormatFile::first();

        $data = [
            'nomor_surat' => 'SM/002/2024',
            'judul_laporan' => 'Updated Laporan',
            'divisi' => 9999, // Non-existent divisi
            'tanggal' => '2024-01-02',
            'perihal' => 'Updated Perihal',
            'pengirim' => 'Updated Pengirim',
            'format_file_id' => $formatFile->id,
        ];

        $response = $this->put(route('surat-masuk.update', $surat->id), $data);

        $response->assertRedirect();
        $response->assertSessionHasErrors('divisi');
    }

    /** @test */
    public function edit_view_handles_null_divisi()
    {
        $user = User::first();
        $this->actingAs($user);

        // Create a surat with null divisi (if possible, or modify existing)
        $surat = SuratMasuk::factory()->create(['divisi_id' => null]);

        $response = $this->get(route('surat-masuk.edit', $surat->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Surat Masuk');
        // Check that the select is not pre-selected if divisi is null
        $response->assertSee('<option value="">Pilih Divisi</option>', false);
    }
}
