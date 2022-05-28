<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasUUID
{
    protected static function bootHasUUID()
    {
        static::creating(function($query) {
            $query->uuid = (string)Str::uuid();
        });
    }
}
