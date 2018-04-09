<?php

namespace Zotyo\Api;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('custom', function ($data = null, int $code = 200, string $message = null) {
            return response()->json([
                'code' => $code,
                'message' => $message,
                'data' => $data
            ], 200);
        });
    }
}
