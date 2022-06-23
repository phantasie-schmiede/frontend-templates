<?php
declare(strict_types=1);

use PSB\PsbTemplates\Domain\Model\Typo3\Page;

return [
    Page::class              => [
        'properties' => [
            'bootstrapContainerType' => [
                'fieldName' => 'tx_psbtemplates_bs_container_type',
            ],
        ],
        'tableName'  => 'pages',
    ],
];
