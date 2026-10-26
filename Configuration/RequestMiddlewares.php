<?php

declare(strict_types=1);

use PSBits\Templates\Middleware\MenuDropdownRedirectMiddleware;

return [
    'frontend' => [
        'psbits/frontend-templates/menu-dropdown-redirect' => [
            'target' => MenuDropdownRedirectMiddleware::class,
            'after'  => [
                'typo3/cms-frontend/page-resolver',
            ],
        ],
    ],
];
