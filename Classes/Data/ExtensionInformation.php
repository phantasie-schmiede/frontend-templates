<?php
declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace PSB\PsbTemplates\Data;

use PSB\PsbFoundation\Data\AbstractExtensionInformation;

/**
 * Class ExtensionInformation
 *
 * @package PSB\PsbTemplates\Data
 */
class ExtensionInformation extends AbstractExtensionInformation
{
    public const ALLOW_LANGUAGE_FILE_EDITING = true;

    public const DOKTYPES = [
        'MENU_DROPDOWN' => 100,
    ];

    public const PAGE_TYPES = [
        self::DOKTYPES['MENU_DROPDOWN'] => [
            'allowedTables' => ['pages'],
            'name'          => 'menuDropdown',
        ],
    ];
}
