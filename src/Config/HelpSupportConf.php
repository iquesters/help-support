<?php

namespace Iquesters\HelpSupport\Config;

use Iquesters\Foundation\Enums\Module;
use Iquesters\Foundation\Support\BaseConf;

class HelpSupportConf extends BaseConf
{
    protected ?string $identifier = Module::HELP_SUPPORT;

    protected int $docs_cache_hours;

    protected function prepareDefault(BaseConf $default_values)
    {
        $default_values->docs_cache_hours = 24;
    }
}
