<?php

namespace App\Console\Commands;

use App\Models\News;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Console\Command;

class GetNews extends Command
{
    protected $signature = 'news:get {--pages=} {--items=} {--base-url=}';
    protected $description = 'Get News';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->baseUrl = $this->option('base-url') ? $this->option('base-url') : env('WEBSITE_BASE_URL');
        $this->newsPerPage = $this->option('items') ? $this->option('items') : env('ITEMS_PER_PAGE');
        $this->maxPages = $this->option('pages') ? $this->option('pages'): env('MAX_PAGES_TO_CRAWL');
        $this->newNews = 0;
        for ($i=1; $i<=$this->maxPages; $i++) {    
            $crawler = $this->getCrawlerForPage($i);
            $crawler->filter('article')->each(function ($article) {
                $headline = $article->filter('.tileHeadline')->text();
                $link = $article->filter('.tileHeadline a')->attr('href');
                $date = $article->filter('.documentByLine .summary-view-icon')->eq(0)->text();
                $time = str_replace('h', ':',$article->filter('.documentByLine .summary-view-icon')->eq(1)->text());
                $published_at = Carbon::createFromFormat("d/m/Y H:i",$date." ".$time)->toDateTimeString();
                
                $news = News::firstOrCreate([
                    'headline' => $headline,
                    'published_at' => $published_at
                ],[
                    'headline' => $headline,
                    'link' => $link,
                    'published_at' => $published_at
                ]);
                if($news->wasRecentlyCreated){
                    $news->text = (new Client())->request('GET', $link)->filter('#parent-fieldname-text')->text();
                    $news->save();
                    $this->newNews +=1;
                }
            });
        }
        if($this->newNews>0){
            $this->info($this->newNews.' new News found and registered');
        }else{
            $this->info('No new News found');
        }
    }

    private function getCrawlerForPage(int $page){
        $endpoint = '/noticias';
        $client = new Client();
        $start = ($this->newsPerPage*$page)-$this->newsPerPage;
        return $client->request('GET', $this->baseUrl.$endpoint.'?b_start:int='.$start);
    }
}
