<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Koleksi extends Model
{
    use HasFactory;

    public function museum(): BelongsTo
    {
        return $this->belongsTo(Museum::class);
    }

    public function mutasis(): HasMany
    {
        return $this->hasMany(Mutasi::class);
    }
}
