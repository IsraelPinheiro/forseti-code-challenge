<?php

namespace App\Observers;

use App\Models\News;
use Illuminate\Support\Facades\Artisan;

class NewsObserver
{
    /**
     * Handle the News "created" event.
     *
     * @param  News  $news
     * @return void
     */
    public function created(News $news)
    {
        //Tag created news
        Artisan::call('news:tag', [
            '--news' => $news->id
        ]);
    }
}
