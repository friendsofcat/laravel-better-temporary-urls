<?php

namespace FriendsOfCat\LaravelBetterTemporaryUrls\Http\Controller;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LocalFilesystemTemporaryUrlController extends Controller
{

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function handle(Request $request)
    {
        try {
            if (!$request->filled('path') || !Storage::exists($request->path)) {
                return response(sprintf('File not found: "%s"', $request->path), 400);
            }

            $file_path = Storage::path($request->path);

            return new BinaryFileResponse($file_path, 200, [], false);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::debug($e->getTraceAsString());

            return response(null, 422);
        }
    }
}

