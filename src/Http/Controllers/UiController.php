<?php

namespace Iquesters\HelpSupport\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;

class UiController extends Controller
{
    // Map view names to their GitHub markdown filenames
    protected array $docMap = [
        'docs.user_users' => 'user_users.md',
        'docs.user_roles' => 'user_roles.md',
        'docs.user_perms' => 'user_perms.md',
    ];

    public function show(string $viewName)
    {
        Log::debug('Help support UI requested', [
            'controller'     => static::class,
            'requested_view' => $viewName,
        ]);

        // If it's a doc page, fetch markdown and render viewer
        if (array_key_exists($viewName, $this->docMap)) {
            return $this->renderDoc($viewName);
        }

        // Otherwise render the blade view directly (e.g. helps.index)
        $bladeView = 'help-support::' . $viewName;

        if (! view()->exists($bladeView)) {
            Log::warning('Help support view not found', [
                'requested_view' => $viewName,
                'blade_view'     => $bladeView,
            ]);
            abort(404, 'Page not found.');
        }

        return view($bladeView);
    }

    protected function renderDoc(string $viewName)
    {
        $filename   = $this->docMap[$viewName];
        $githubRepo = 'https://raw.githubusercontent.com/glitched-matrix44/user-management/';
        $branch     = 'IQU-docs-8';
        $url        = $githubRepo . $branch . '/docs/' . $filename;

        Log::debug('Fetching markdown from', ['url' => $url]);

        $html  = null;
        $title = ucfirst(str_replace(['docs.', '_'], ['', ' '], $viewName));

        $response = Http::get($url);

        if ($response->successful()) {
            $converter = new CommonMarkConverter();
            $html      = $converter->convert($response->body())->getContent();
        } else {
            Log::warning('Markdown fetch failed', ['url' => $url, 'status' => $response->status()]);
        }

        return view('help-support::docs.viewer', [
            'content' => $html,
            'title'   => $title,
        ]);
    }
}