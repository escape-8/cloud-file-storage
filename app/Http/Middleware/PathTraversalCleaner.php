<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PathTraversalCleaner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->get('path');
        if ($path) {
            $decodePath = urldecode($path);
            $cleanTraverseSymbols = str_replace(['../', '..'], null, $decodePath);
            $request->query->set('path', urlencode($cleanTraverseSymbols));
        }

        return $next($request);
    }
}
