<?php

namespace Iquesters\HelpSupport\Database\Seeders;

use Iquesters\Foundation\Database\Seeders\BaseSeeder;

class HelpSupportSeeder extends BaseSeeder
{
    protected string $moduleName = 'help-support';

    protected string $description = 'Help and support module';

    protected array $metas = [
        'module_icon' => 'fa-solid fa-circle-question',
        'module_sidebar_menu' => [
            [
                'icon' => 'fa-solid fa-book-open',
                'label' => 'Help Center',
                'route' => 'helpsupport.ui.show',
                'params' => [
                    'viewName' => 'helps.index',
                ],
            ],
        ],
    ];

    protected function seedCustom(): void
    {
    }
}
