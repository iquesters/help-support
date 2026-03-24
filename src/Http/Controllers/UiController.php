<?php

namespace Iquesters\HelpSupport\Http\Controllers;

use Iquesters\Foundation\Enums\Module;
use Iquesters\Foundation\Support\ConfProvider;
use Iquesters\Foundation\System\Traits\Loggable;
use Iquesters\HelpSupport\Constants\Constants;
use Iquesters\HelpSupport\Constants\EntityStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request; //file content cache

class UiController extends Controller
{
    use Loggable;

    public function show(string $viewName): View
    {
        $this->logMethodStart("Requested view: {$viewName}");

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

            $this->logWarning("View not found: {$resolvedView}");

            throw new NotFoundHttpException(Constants::ERROR_VIEW_NOT_FOUND);
        }

        $this->logMethodEnd("Resolved view: {$resolvedView}");

        return view($resolvedView);
    }

    public function getModuleDocs(string $module)
    {
        $this->logMethodStart("Module docs requested: {$module}");

        $cacheHours = $this->getDocsCacheHours();
        $roleNames = $this->getCurrentUserRoleNames();
        $visibilityScope = $this->canViewAllDocs()
            ? Constants::VISIBILITY_SCOPE_ALL
            : Constants::VISIBILITY_SCOPE_DEFAULT;

        $this->logInfo(
            'Docs access context'
            . ' | module=' . $module
            . ' | roles=' . json_encode($roleNames)
            . ' | full_access_roles=' . json_encode($this->getDocsFullAccessRoles())
            . ' | visibility_scope=' . $visibilityScope
        );

        $files = Cache::remember($this->makeDocsCacheKey($module, $visibilityScope), now()->addHours($cacheHours), function () use ($module) {
            return collect($this->fetchModuleDocsRecursively($module))
                ->filter(fn ($item) => $this->isDocPathVisible($item['path'] ?? ''))
                ->values()
                ->all();
        });

        $this->logInfo("Docs visible for {$module}: " . count($files));
        $this->logMethodEnd("Visibility scope: {$visibilityScope}");

        return response()->json($files);
    }
    
    // Added to ensure file content is part of the cache.
    public function getDocFile(Request $request)
    {
        $this->logMethodStart('Raw doc file requested');

        $url = $request->query('url');
        if (!$url) {
            $this->logWarning('No URL provided for raw doc file');
            return response()->json(['error' => Constants::ERROR_NO_URL_PROVIDED], 400);
        }

        $docPath = $this->extractGithubDocPath($url);

        if (! $docPath || ! $this->isDocPathVisible($docPath)) {
            $this->logWarning("Unauthorized document access attempt: {$url}");
            return response()->json(['error' => Constants::ERROR_UNAUTHORIZED_DOCUMENT_ACCESS], 403);
        }

        // Cache the raw markdown file content using a hash of the URL as the key
        $cacheKey = $this->makeDocFileCacheKey($url);
        $cacheHours = $this->getDocsCacheHours();

        $content = Cache::remember($cacheKey, now()->addHours($cacheHours), function () use ($url, $docPath) {
            $this->logInfo("Fetching raw GitHub doc: {$docPath}");

            // Only hits raw.githubusercontent.com on first load or after cache expires
            $response = Http::get($url);

            if (! $response->ok()) {
                $this->logWarning("Raw GitHub doc fetch failed [status={$response->status()}]: {$docPath}");
                return null;
            }

            return $response->ok() ? $response->body() : null;
        });

        if ($content === null) {
            $this->logWarning("No content returned for doc path: {$docPath}");
            return response()->json(['error' => Constants::ERROR_DOCUMENT_NOT_FOUND], 404);
        }

        $this->logMethodEnd("Doc path: {$docPath}");

        return response($content)->header('Content-Type', Constants::RESPONSE_TEXT_PLAIN);
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
        $conf = $this->getHelpSupportConf();

        if ($conf) {
            return max(1, (int) ($conf->docs_cache_hours ?? 24));
        }

        return 24;
    }

    protected function getDocsRepositoryOwner(): string
    {
        $conf = $this->getHelpSupportConf();

        return (string) ($conf->docs_repository_owner ?? 'iquesters');
    }

    protected function getDocsRootPath(): string
    {
        $conf = $this->getHelpSupportConf();
        $rootPath = (string) ($conf->docs_root_path ?? 'docs/');

        return rtrim($rootPath, '/') . '/';
    }

    protected function getDocsDefaultBranch(): string
    {
        $conf = $this->getHelpSupportConf();

        return (string) ($conf->docs_default_branch ?? 'main');
    }

    protected function getDocsFullAccessRoles(): array
    {
        $conf = $this->getHelpSupportConf();

        return collect($conf->docs_full_access_roles ?? ['super-admin', 'iq-developer'])
            ->filter(fn ($role) => is_string($role) && $role !== '')
            ->values()
            ->all();
    }

    protected function getDocsDefaultVisiblePaths(): array
    {
        $conf = $this->getHelpSupportConf();
        $docsRootPath = $this->getDocsRootPath();

        return collect($conf->docs_default_visible_paths ?? [$docsRootPath . 'users/'])
            ->filter(fn ($path) => is_string($path) && $path !== '')
            ->values()
            ->all();
    }

    protected function getHelpSupportConf(): ?object
    {
        try {
            $conf = ConfProvider::from(Module::HELP_SUPPORT);

            if (method_exists($conf, 'ensureLoaded')) {
                $conf->ensureLoaded();
            }

            return $conf;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function fetchModuleDocsRecursively(string $module): array
    {
        $this->logInfo("Fetching GitHub repo metadata for module: {$module}");

        $headers = [
            'Accept' => Constants::GITHUB_ACCEPT_HEADER,
        ];
        $repositoryOwner = $this->getDocsRepositoryOwner();
        $docsRootPath = $this->getDocsRootPath();

        $repoResponse = Http::withHeaders($headers)
            ->get("https://api.github.com/repos/{$repositoryOwner}/{$module}");

        if (! $repoResponse->ok()) {
            $this->logWarning("GitHub repo metadata fetch failed [status={$repoResponse->status()}] for module: {$module}");
            return [];
        }

        $defaultBranch = $repoResponse->json('default_branch', $this->getDocsDefaultBranch());

        $this->logInfo("Fetching recursive GitHub docs tree for module: {$module}, branch: {$defaultBranch}");

        $treeResponse = Http::withHeaders($headers)
            ->get("https://api.github.com/repos/{$repositoryOwner}/{$module}/git/trees/{$defaultBranch}", [
                'recursive' => 1,
            ]);

        if (! $treeResponse->ok()) {
            $this->logWarning("GitHub docs tree fetch failed [status={$treeResponse->status()}] for module: {$module}, branch: {$defaultBranch}");
            return [];
        }

        $tree = $treeResponse->json('tree', []);

        $docs = collect($tree)
            ->filter(function ($item) use ($docsRootPath) {
                $path = $item['path'] ?? '';

                return ($item['type'] ?? null) === 'blob'
                    && str_starts_with($path, $docsRootPath)
                    && str_ends_with(strtolower($path), '.md');
            })
            ->map(function ($item) use ($module, $defaultBranch, $repositoryOwner) {
                $path = $item['path'];

                return [
                    'name' => basename($path),
                    'path' => $path,
                    'download_url' => "https://" . Constants::GITHUB_RAW_HOST . "/{$repositoryOwner}/{$module}/{$defaultBranch}/{$path}",
                    'html_url' => "https://github.com/{$repositoryOwner}/{$module}/blob/{$defaultBranch}/{$path}",
                    'type' => 'file',
                    'status' => EntityStatus::ACTIVE,
                ];
            })
            ->sortBy('path')
            ->values()
            ->all();

        $this->logInfo("GitHub docs tree loaded for module {$module}: " . count($docs) . ' markdown files found');

        return $docs;
    }

    protected function canViewAllDocs(): bool
    {
        $roleNames = $this->getCurrentUserRoleNames();

        if (empty($roleNames)) {
            return false;
        }

        foreach ($this->getDocsFullAccessRoles() as $role) {
            if (in_array($role, $roleNames, true)) {
                return true;
            }
        }

        return false;
    }

    protected function isDocPathVisible(string $path): bool
    {
        if ($this->canViewAllDocs()) {
            return true;
        }

        foreach ($this->getDocsDefaultVisiblePaths() as $allowedPathPrefix) {
            if (str_starts_with($path, $allowedPathPrefix)) {
                return true;
            }
        }

        return false;
    }

    protected function extractGithubDocPath(string $url): ?string
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        $path = trim($parsedUrl['path'] ?? '', '/');
        $repositoryOwner = $this->getDocsRepositoryOwner();

        if ($host !== Constants::GITHUB_RAW_HOST || $path === '') {
            return null;
        }

        $segments = explode('/', $path, 5);

        if (count($segments) < 5 || $segments[0] !== $repositoryOwner) {
            return null;
        }

        return $segments[4];
    }

    protected function makeDocsCacheKey(string $module, string $visibilityScope): string
    {
        return Constants::CACHE_KEY_DOCS_PREFIX . $module . '_' . $visibilityScope;
    }

    protected function makeDocFileCacheKey(string $url): string
    {
        return Constants::CACHE_KEY_FILE_PREFIX . md5($url);
    }

    protected function getCurrentUserRoleNames(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        try {
            if (isset($user->roles)) {
                return collect($user->roles)
                    ->pluck('name')
                    ->filter(fn ($role) => is_string($role) && $role !== '')
                    ->values()
                    ->all();
            }

            if (method_exists($user, 'roles')) {
                return $user->roles()
                    ->pluck('name')
                    ->filter(fn ($role) => is_string($role) && $role !== '')
                    ->values()
                    ->all();
            }
        } catch (\Throwable $e) {
            $this->logWarning('Failed to resolve current user roles: ' . $e->getMessage());
        }

        return [];
    }
}
