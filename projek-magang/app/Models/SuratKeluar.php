<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluar';
    protected $fillable = [
        'nomor_surat',
        'judul_laporan',
        'tanggal_kirim',
        'penerima',
        'perihal',
        'instruksi_disposisi',
        'instruksi_tambahan',
        'divisi_id',
        'status',
        'user_id',
        'file_path',
        'format_file_id',
        'created_by',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function formatFile()
    {
        return $this->belongsTo(FormatFile::class, 'format_file_id');
    }
}
