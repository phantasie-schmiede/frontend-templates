<?php
declare(strict_types=1);

$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['fal_media']['config']['overrideChildTca']['columns']['crop']['config']['cropVariants'] = [
    'cardLandscape' => [
        'title'               => 'Card (landscape)',
        'allowedAspectRatios' => [
            '16:9' => [
                'title' => '16:9',
                'value' => 16 / 9,
            ],
        ],
    ],
    'cardPortrait'  => [
        'title'               => 'Card (portrait)',
        'allowedAspectRatios' => [
            '3:4' => [
                'title' => '3:4',
                'value' => 3 / 4,
            ],
        ],
    ],
];
