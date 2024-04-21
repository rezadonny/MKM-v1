<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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

    protected static function boot() /* untuk menghapus file upload dari storage */
    {
        parent::boot();
        static::updating(function ($model) {
            if ($model->isDirty('foto_utama') && ($model->getOriginal('foto_utama') !== null)) {
                Storage::disk('public')->delete($model->getOriginal('foto_utama'));
            }

            if ($model->isDirty('logo') && ($model->getOriginal('logo') !== null)) {
                Storage::disk('public')->delete($model->getOriginal('logo'));
            }
        });
    }
}
