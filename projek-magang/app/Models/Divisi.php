<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';

    /**
     * Mendefinisikan relasi one-to-many ke SuratMasuk.
     */
    public function suratMasuk()
    {
        return $this->hasMany(SuratMasuk::class);
    }

    /**
     * Mendefinisikan relasi one-to-many ke SuratKeluar.
     */
    public function suratKeluar()
    {
        return $this->hasMany(SuratKeluar::class);
    }
}