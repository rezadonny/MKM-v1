<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Museum extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'website',
        'medsos_instagram',
        'medsos_twitter',
        'medsos_facebook',
        'medsos_tiktok',
        'googlemap',
        'tipe_koleksi',
        'tipe_pengelola',
        'tipe_area',
        'tipe_audience',
        'tipe_pameran',
        'foto_utama',
        'logo',
        'keterangan',
        'tanggal_berdiri',
        'pengelola',
        'slug',
    ];

    public function koleksis(): HasMany
    {
        return $this->hasMany(Koleksi::class);
    }
}
