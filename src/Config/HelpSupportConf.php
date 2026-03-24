<?php

namespace Iquesters\HelpSupport\Config;

use Iquesters\Foundation\Enums\Module;
use Iquesters\Foundation\Support\BaseConf;

class HelpSupportConf extends BaseConf
{
    protected ?string $identifier = Module::HELP_SUPPORT;

    protected int $docs_cache_hours;
    protected string $docs_repository_owner;
    protected string $docs_root_path;
    protected string $docs_default_branch;
    protected array $docs_full_access_roles;
    protected array $docs_default_visible_paths;

    protected function prepareDefault(BaseConf $default_values)
    {
        $default_values->docs_cache_hours = 24;
        $default_values->docs_repository_owner = 'iquesters';
        $default_values->docs_root_path = 'docs/';
        $default_values->docs_default_branch = 'main';
        $default_values->docs_full_access_roles = [
            'super-admin',
            'iq-developer',
        ];
        $default_values->docs_default_visible_paths = [
            'docs/users/',
            'docs/shared/',
        ];
    }
}
