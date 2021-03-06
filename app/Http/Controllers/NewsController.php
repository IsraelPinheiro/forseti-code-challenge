<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Tag;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use League\Csv\Writer;
use SplTempFileObject;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $request->validate([
                'limit' => ['sometimes', 'required', 'numeric', 'min:1'],
                'offset' => ['sometimes', 'required', 'numeric', 'min:1'],
                'published_from' => ['sometimes', 'required', 'date', 'before_or_equal:published_to'],
                'published_to' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
                'tags' => ['sometimes', 'required', 'array', 'min:1']
            ]);

            if($request->has('tags')){
                $tags = Tag::whereIn('tag', $request->tags)->pluck('id');
            }else{
                $tags = [];
            }
            
            $news = News::when($request->has('offset'), function($query) use($request){
                $query->skip($request->offset);
            })->when($request->has('limit'), function($query) use($request){
                $query->take($request->limit);
            })->when($request->has('published_from'), function($query) use($request){
                $query->where('created_at','>=',$request->published_from);
            })->when($request->has('published_to'), function($query) use($request){
                $query->where('created_at','<=',$request->published_to);
            })->when($tags, function($query) use($tags){
                $query->whereHas('tags', function($query) use($tags){
                    $query->whereIn('id', $tags);
                });
            })->get();

            return $this->success($news);
        } catch (Exception $exception) {
            return $this->error(
                $exception->getMessage(),
                [
                    'exception_code' => $exception->getCode(),
                    'exception_type' => gettype($exception),
                    'exception_trace' => $exception->getTraceAsString()
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Request $request
     * @param  string $uuid
     * @return JsonResponse
     */
    public function show(Request $request, string $uuid):JsonResponse
    {
        try {
            $request->merge([
                'uuid' => $uuid
            ])->validate([
                'uuid' => ['required', 'uuid', 'exists:news,uuid']
            ]);

            $news = News::where('uuid',$uuid)->firstOrFail();
            return $this->success($news);
        } catch (Exception $exception) {
            return $this->error(
                $exception->getMessage(),
                [
                    'exception_code' => $exception->getCode(),
                    'exception_type' => gettype($exception),
                    'exception_trace' => $exception->getTraceAsString()
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @param  string $uuid
     * @return JsonResponse
     */
    public function destroy(Request $request, string $uuid):JsonResponse
    {
        try {
            $request->merge([
                'uuid' => $uuid
            ])->validate([
                'uuid' => ['required', 'uuid', 'exists:news,uuid']
            ]);

            $news = News::where('uuid',$uuid)->firstOrFail();
            $news->delete();
            return $this->success($news, 'Deleted');
        } catch (Exception $exception){
            return $this->error(
                $exception->getMessage(),
                [
                    'exception_code' => $exception->getCode(),
                    'exception_type' => gettype($exception),
                    'exception_trace' => $exception->getTraceAsString()
                ]
            );
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function exportToCsv(Request $request)
    {
        try {
            $request->validate([
                'limit' => ['sometimes', 'required', 'numeric', 'min:1'],
                'offset' => ['sometimes', 'required', 'numeric', 'min:1'],
                'published_to' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
                'published_from' => ['sometimes', 'required', 'date', 'before_or_equal:published_to'],
                'tags' => ['sometimes', 'required', 'array', 'min:1']
            ]);

            if($request->has('tags')){
                $tags = Tag::whereIn('tag', $request->tags)->pluck('id');
            }
            
            $news = News::when($request->has('offset'), function($query) use($request){
                $query->skip($request->offset);
            })->when($request->has('limit'), function($query) use($request){
                $query->take($request->limit);
            })->when($request->has('published_from'), function($query) use($request){
                $query->where('created_at','>=',$request->published_from);
            })->when($request->has('published_to'), function($query) use($request){
                $query->where('created_at','<=',$request->published_to);
            })->when($tags, function($query) use($tags){
                $query->whereHas('tags', function($query) use($tags){
                    $query->whereIn('id', $tags);
                });
            })->get();

            $csv = Writer::createFromFileObject(new SplTempFileObject());
            $columns = Schema::getColumnListing((new News())->getTable());
            array_push($columns, 'tags');
            $csv->insertOne($columns);

            if($news->count()){
                foreach ($news as $item) {
                    $csv->insertOne($item->toArray());
                }
            }
            
            return response((string) $csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Disposition' => 'attachment; filename="news.csv"',
            ]);

        } catch (Exception $exception) {
            return $this->error(
                $exception->getMessage(),
                [
                    'exception_code' => $exception->getCode(),
                    'exception_type' => gettype($exception),
                    'exception_trace' => $exception->getTraceAsString()
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @param  string $uuid
     * @return JsonResponse
     */
    public function exportOneToCsv(Request $request, string $uuid)
    {
        try {
            $request->merge([
                'uuid' => $uuid
            ])->validate([
                'uuid' => ['required', 'uuid', 'exists:news,uuid']
            ]);

            $news = News::where('uuid',$uuid)->firstOrFail();
            
            $csv = Writer::createFromFileObject(new SplTempFileObject());
            $columns = Schema::getColumnListing((new News())->getTable());
            array_push($columns, 'tags');
            $csv->insertOne($columns);
            $csv->insertOne($news->toArray());
            
            return response((string) $csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Disposition' => 'attachment; filename="news.csv"',
            ]);
        } catch (Exception $exception) {
            return $this->error(
                $exception->getMessage(),
                [
                    'exception_code' => $exception->getCode(),
                    'exception_type' => gettype($exception),
                    'exception_trace' => $exception->getTraceAsString()
                ]
            );
        }
    }
}
