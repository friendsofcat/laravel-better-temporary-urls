<?php

// Temporary URLs fron routes are only available on local envs
if (config('filesystems.default', 'local') === 'local') {
    Route::get('temporary', 'LocalFilesystemTemporaryUrlController@handle')
        ->name('lbtu.temporary-url')
        ->middleware(\Illuminate\Routing\Middleware\ValidateSignature::class);
}
