<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function check():JsonResponse
    {        
        try {
            DB::connection()->getPDO();
            $database_status = 'Connected';
        } catch (Exception $exception) {
            $database_status = 'Failed to Connect';
        }

        return response()->json([
            'application' => [
                'status' => 'Online',
                'name' => config('app.name'),
                'version' => config('app.version'),
                'env' => config('app.env'),
            ],
            'database' => [
                'status' => $database_status
            ]
        ]);
    }
}
