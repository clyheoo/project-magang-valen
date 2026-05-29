<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    use HasFactory;

    protected $table = 'riwayat_status';

    protected $fillable = [
        'arsip_id',
        'surat_masuk_id',
        'status',
        'user_id',
        'keterangan',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function arsip()
    {
        return $this->belongsTo(Arsip::class, 'arsip_id');
    }

    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
