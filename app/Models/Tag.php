<?php

namespace App\Models;

use App\Models\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, HasUUID;

    protected $fillable = [
        'tag',
    ];

    /**
     * The News that were tagged with this Tag.
     */
    public function news()
    {
        return $this->belongsToMany(News::class)->withPivot('occurrences');
    }
}
