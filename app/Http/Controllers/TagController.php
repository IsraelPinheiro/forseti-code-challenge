<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index():JsonResponse
    {
        try {
            $tags = Tag::all();
            return $this->success($tags);
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
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function store(Request $request):JsonResponse
    {
        try {
            $request->validate([
                'tag' => ['required', 'string', 'min:3', Rule::unique('tags')]
            ]);
            
            $tag = Tag::create([
                'tag' => $request->tag
            ]);
            
            return $this->success($tag);
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
    public function news(Request $request, string $uuid):JsonResponse
    {
        try {
            $request->merge([
                'uuid' => $uuid
            ])->validate([
                'uuid' => ['required', 'uuid', 'exists:tags,uuid']
            ]);

            $tag = Tag::where('uuid',$uuid)->firstOrFail();
            return $this->success($tag->news);
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
                'uuid' => ['required', 'uuid', 'exists:tags,uuid']
            ]);

            $tag = Tag::where('uuid',$uuid)->firstOrFail();
            $tag->delete();
            return $this->success($tag, 'Deleted');
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
}
