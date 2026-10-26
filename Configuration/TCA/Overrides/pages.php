<?php

declare(strict_types=1);

use PSBits\FrontendTemplates\Data\ExtensionInformation;

defined('TYPO3') or die();

(static function() {
    $columnsOverrides = [
        'no_index'  => [
            'config' => [
                'default'  => true,
                'readOnly' => true,
            ],
        ],
        'no_follow' => [
            'config' => [
                'default'  => true,
                'readOnly' => true,
            ],
        ],
    ];

    /*
     * Pages of type menuDropdown shall never be indexed by search engines as they only serve as navigation
     * placeholders.
     */
    $GLOBALS['TCA']['pages']['types'][ExtensionInformation::DOKTYPES['MENU_DROPDOWN']] = array_merge(
        $GLOBALS['TCA']['pages']['types'][ExtensionInformation::DOKTYPES['MENU_DROPDOWN']] ?? [],
        ['columnsOverrides' => $columnsOverrides],
    );
})();
