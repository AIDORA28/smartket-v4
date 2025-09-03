<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FeatureFlagService;
use Symfony\Component\HttpFoundation\Response;

class FeatureGuard
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (!app(FeatureFlagService::class)->isEnabled($feature)) {
            abort(403, "La funcionalidad '$feature' no está disponible en su plan");
        }

        return $next($request);
    }
}
