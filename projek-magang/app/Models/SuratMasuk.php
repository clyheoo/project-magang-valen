<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SuratMasuk extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'surat_masuk';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nomor_surat',
        'tanggal',
        'pengirim',
        'divisi_id',
        'nama_divisi',
        'status',
        'file_path',
        'ukuran',
        'format_file_id',
        'instruksi_disposisi',
        'instruksi_tambahan',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal' => 'date', // Otomatis konversi ke objek Carbon
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function formatFile()
    {
        return $this->belongsTo(FormatFile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class, 'surat_masuk_id');
    }
}
