<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Data;

use PSBits\Foundation\Data\AbstractExtensionInformation;
use PSBits\Foundation\Data\PageTypeConfiguration;

/**
 * Class ExtensionInformation
 *
 * @package PSBits\FrontendTemplates\Data
 */
class ExtensionInformation extends AbstractExtensionInformation
{
    public const array DOKTYPES = [
        'MENU_DROPDOWN' => 100,
    ];

    public function __construct()
    {
        parent::__construct();
        $this->addPageType(
            new PageTypeConfiguration(
                allowedTables: ['pages'],
                doktype: self::DOKTYPES['MENU_DROPDOWN'],
                name: 'menuDropdown',
            )
        );
    }
}
