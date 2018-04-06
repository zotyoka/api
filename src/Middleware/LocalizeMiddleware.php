<?php
namespace Zotyo\Api\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocalizeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('locale');
        app()->setLocale($locale);
        
        return $next($request);
    }
}
