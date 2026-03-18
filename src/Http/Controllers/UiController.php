<?php

namespace Iquesters\HelpSupport\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UiController extends Controller
{
    public function show(string $viewName): View
    {
        $resolvedView = $this->resolvePackageViewName($viewName);

        Log::debug('Help support UI view requested', [
            'controller' => static::class,
            'requested_view' => $viewName,
            'resolved_view' => $resolvedView,
        ]);

        if (!view()->exists($resolvedView)) {
            Log::warning('Help support UI view not found', [
                'controller' => static::class,
                'requested_view' => $viewName,
                'resolved_view' => $resolvedView,
            ]);

            throw new NotFoundHttpException('Help support view not found.');
        }

        return view($resolvedView);
    }

    public function getModuleDocs(string $module)
    {
        $files = Cache::remember("github_docs_{$module}", now()->addHours(6), function () use ($module) {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json'
            ])->get("https://api.github.com/repos/iquesters/{$module}/contents/docs");

            return $response->ok() ? $response->json() : [];
        });

        return response()->json($files);
    }

    protected function resolvePackageViewName(string $viewName): string
    {
        $normalizedView = trim($viewName);
        $normalizedView = str_replace('/', '.', $normalizedView);
        $normalizedView = preg_replace('/\.+/', '.', $normalizedView);
        $normalizedView = ltrim((string) $normalizedView, '.');

        if (str_starts_with($normalizedView, 'help-support::')) {
            return $normalizedView;
        }

        return 'help-support::' . $normalizedView;
    }
}
