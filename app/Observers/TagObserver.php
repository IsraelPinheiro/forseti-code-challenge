<?php

namespace App\Observers;

use App\Models\Tag;
use Illuminate\Support\Facades\Artisan;

class TagObserver
{
    /**
     * Handle the Tag "created" event.
     *
     * @param  Tag  $tag
     * @return void
     */
    public function created(Tag $tag)
    {
        //Tag news for the newly created tag
        Artisan::call('news:tag');
    }

    /**
     * Handle the Tag "deleting" event.
     *
     * @param  Tag  $tag
     * @return void
     */
    public function deleting(Tag $tag)
    {
        $tag->news()->detach();
    }
}
