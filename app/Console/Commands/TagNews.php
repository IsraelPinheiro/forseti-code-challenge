<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Tag;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TagNews extends Command
{
    protected $signature = 'news:tag {--news=} {--include-tagged}';
    protected $description = 'Auto add tags to news';

    private $tags;

    public function __construct()
    {
        parent::__construct();
        $this->tags = Tag::all();
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(!$this->tags->count()>0){
            $this->error("No Tags found");
            return 0;
        }

        try{
            if($this->option('news')){
                $news = News::when(!$this->option('include-tagged'), function($query){
                    return $query->where('tagged', false);
                })->findOrFail($this->option('news'));
                $this->tagNews($news);
            } else{
                $news = News::when(!$this->option('include-tagged'), function($query){
                    return $query->where('tagged', false);
                })->get();
                
                if($news->count()){
                    $this->withProgressBar($news, function($news){
                        $this->info(" Tagging $news->uuid");
                        $this->tagNews($news);
                    });
                } else{
                    $this->info("Now news to be tagged");
                }
            }
        } catch (ModelNotFoundException $exception) {
            $this->error("No News found with id ".$this->option('news')." and the selected options");
            return 0;
        } catch (Exception $exception){
            throw $exception;
        }
    }

    private function tagNews(News $news){
        try {
            $text = strtolower($news->text);
            $tagsFound = [];
            foreach ($this->tags as $tag) {
                $tagOccurrences = substr_count($text, strtolower($tag->tag));
                if($tagOccurrences>0){
                    $tagsFound[$tag->id] = ['occurrences' => $tagOccurrences];
                    $news->tagged = true;
                    $news->save();
                }
            }
            $news->tags()->sync($tagsFound);
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
