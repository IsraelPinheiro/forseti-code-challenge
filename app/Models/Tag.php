<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag',
    ];

    /**
     * The News that were tagged with this Tag.
     */
    public function news()
    {
        return $this->belongsToMany(News::class);
    }
}
