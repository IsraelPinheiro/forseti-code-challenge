<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Tag;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
                'date_to' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
                'date_from' => ['sometimes', 'required', 'date', 'before_or_equal:date_to'],
                'tags' => ['sometimes', 'required', 'array', 'min:1']
            ]);

            if($request->has('tags')){
                $tags = Tag::whereIn('tag', $request->tags)->pluck('id');
            }
            
            $news = News::when($request->has('offset'), function($query) use($request){
                $query->skip($request->offset);
            })->when($request->has('limit'), function($query) use($request){
                $query->take($request->limit);
            })->when($request->has('date_from'), function($query) use($request){
                $query->where('created_at','>=',$request->date_from);
            })->when($request->has('date_to'), function($query) use($request){
                $query->where('created_at','<=',$request->date_to);
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
    public function exportToCsv(Request $request):JsonResponse
    {
        try {
            $request->validate([
                'limit' => ['sometimes', 'required', 'numeric', 'min:1'],
                'offset' => ['sometimes', 'required', 'numeric', 'min:1'],
                'date_to' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
                'date_from' => ['sometimes', 'required', 'date', 'before_or_equal:date_to'],
                'tags' => ['sometimes', 'required', 'array', 'min:1']
            ]);

            if($request->has('tags')){
                $tags = Tag::whereIn('tag', $request->tags)->pluck('id');
            }
            
            $news = News::when($request->has('offset'), function($query) use($request){
                $query->skip($request->offset);
            })->when($request->has('limit'), function($query) use($request){
                $query->take($request->limit);
            })->when($request->has('date_from'), function($query) use($request){
                $query->where('created_at','>=',$request->date_from);
            })->when($request->has('date_to'), function($query) use($request){
                $query->where('created_at','<=',$request->date_to);
            })->when($tags, function($query) use($tags){
                $query->whereHas('tags', function($query) use($tags){
                    $query->whereIn('id', $tags);
                });
            })->get();

            $csv = Writer::createFromFileObject(new SplTempFileObject());
            $csv->insertOne(Schema::getColumnListing((new News())->getTable()));

            if($news->count()){
                $csv->insertAll($news);
            }
            
            return response((string) $csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Disposition' => 'attachment; filename="people.csv"',
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
    public function exportOneToCsv(Request $request, string $uuid):JsonResponse
    {
        try {
            $request->merge([
                'uuid' => $uuid
            ])->validate([
                'uuid' => ['required', 'uuid', 'exists:news,uuid']
            ]);

            $news = News::where('uuid',$uuid)->firstOrFail();
            
            $csv = Writer::createFromFileObject(new SplTempFileObject());
            $csv->insertOne(Schema::getColumnListing((new News())->getTable()));
            $csv->insertOne($news);
            
            return response((string) $csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Disposition' => 'attachment; filename="people.csv"',
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
