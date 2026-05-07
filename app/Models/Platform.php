<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'version',
        'price',
        'zip_path',
        'image_path',
        'is_active',
    ];

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }
}
