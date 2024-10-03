<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});






Route::get('/link', function () {
    try {
        // Run the artisan command to create the storage link
        Artisan::call('storage:link');

        return 'Storage link created successfully!';
    } catch (\Exception $e) {
        // Handle any exceptions that might occur
        return 'Error creating storage link: ' . $e->getMessage();
    }
});






Route::get('/clear', function () {
    try {
        // Run the artisan command to create the storage link
        Artisan::call('route:clear');

        return 'Storage link created successfully!';
    } catch (\Exception $e) {
        // Handle any exceptions that might occur
        return 'Error creating storage link: ' . $e->getMessage();
    }
});



Route::get('/clear-cache', function () {
    try {
        // Run the artisan commands
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('optimize');

        return 'Cache cleared, configuration cache cleared, and application optimized successfully!';
    } catch (\Exception $e) {
        // Handle any exceptions that might occur
        return 'Error: ' . $e->getMessage();
    }
});

require __DIR__.'/auth.php';
