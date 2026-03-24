<?php

namespace Iquesters\HelpSupport\Http\Controllers;

use Iquesters\Foundation\Enums\Module;
use Iquesters\Foundation\Support\ConfProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request; //file content cache

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
        $cacheHours = $this->getDocsCacheHours();

        $files = Cache::remember("github_docs_{$module}", now()->addHours($cacheHours), function () use ($module) {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json'
            ])->get("https://api.github.com/repos/iquesters/{$module}/contents/docs");

            return $response->ok() ? $response->json() : [];
        });

        return response()->json($files);
    }
    
    // Added to ensure file content is part of the cache.
    public function getDocFile(Request $request)
    {
        $url = $request->query('url');
        if (!$url) return response()->json(['error' => 'No URL provided'], 400);

        // Cache the raw markdown file content using a hash of the URL as the key
        $cacheKey = 'github_file_' . md5($url);
        $cacheHours = $this->getDocsCacheHours();

        $content = Cache::remember($cacheKey, now()->addHours($cacheHours), function () use ($url) {
            // Only hits raw.githubusercontent.com on first load or after cache expires
            $response = Http::get($url);
            return $response->ok() ? $response->body() : null;
        });

        return response($content)->header('Content-Type', 'text/plain');
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

    protected function getDocsCacheHours(): int
    {
        try {
            $conf = ConfProvider::from(Module::HELP_SUPPORT);

            if (method_exists($conf, 'ensureLoaded')) {
                $conf->ensureLoaded();
            }

            return max(1, (int) ($conf->docs_cache_hours ?? 24));
        } catch (\Throwable) {
            return 24;
        }
    }
}
