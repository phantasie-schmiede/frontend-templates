<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Utility\Component;

/**
 * Class CardUtility
 *
 * @package PSBits\Templates\Utility\Component
 */
class CardUtility
{
    public static function getTcaCropVariants(): array
    {
        return [
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
    }
}
