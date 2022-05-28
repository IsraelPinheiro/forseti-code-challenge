<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'headline',
        'link',
        'published_at'
    ];

    protected $dates = [
        'published_at'
    ];

    /**
     * The tags that this news was tagged with.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
