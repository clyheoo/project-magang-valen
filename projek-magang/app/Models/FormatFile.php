<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormatFile extends Model
{
    use HasFactory;

    protected $table = 'format_file';

    protected $fillable = [
        'nama_format',
    ];
}
