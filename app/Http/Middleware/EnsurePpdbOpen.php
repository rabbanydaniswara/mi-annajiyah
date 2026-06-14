<?php

namespace App\Http\Middleware;

use App\Helpers\PpdbHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePpdbOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = PpdbHelper::settings();

        if (! $settings['is_open']) {
            return response()->json([
                'success' => false,
                'code' => 'ppdb_closed',
                'message' => $settings['closed_message'],
            ], 403);
        }

        return $next($request);
    }
}
