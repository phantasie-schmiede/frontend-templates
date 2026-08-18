<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Data;

use PSBits\Foundation\Data\AbstractExtensionInformation;

/**
 * Class ExtensionInformation
 *
 * @package PSBits\Templates\Data
 */
class ExtensionInformation extends AbstractExtensionInformation
{
    public const array DOKTYPES = [
        'MENU_DROPDOWN' => 100,
    ];

    public const array PAGE_TYPES = [
        self::DOKTYPES['MENU_DROPDOWN'] => [
            'allowedTables' => ['pages'],
            'name'          => 'menuDropdown',
        ],
    ];
}
