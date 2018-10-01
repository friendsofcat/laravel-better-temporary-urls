<?php

use Illuminate\Routing\Middleware\ValidateSignature;

// Temporary URLs fron routes are only available on local envs.
if (config('laravel-better-temporary-urls.adapters.local', true)) {
    Route::get('temporary', 'LocalFilesystemTemporaryUrlController@handle')
        ->name('lbtu.temporary-url')
        ->middleware(ValidateSignature::class);
}
