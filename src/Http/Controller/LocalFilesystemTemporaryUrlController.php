<?php

namespace FriendsOfCat\LaravelBetterTemporaryUrls\Http\Controller;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LocalFilesystemTemporaryUrlController extends Controller
{

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        try {
            if (!$request->filled('path') || !Storage::exists($request->path)) {
                return response('');
            }

            return response()->file(Storage::path($request->path), [
                'Cache-Control' => 'no-cache',
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::debug($e->getTraceAsString());

            return response(null, 422);
        }
    }
}

