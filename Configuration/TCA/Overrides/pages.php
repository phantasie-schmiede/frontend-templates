<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
declare(strict_types = 1);

defined('TYPO3_MODE') or die();

(static function () {
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
     * When installing this extensions the TCA is built before the setup signal slot is fired, which registers the
     * ExtensionInformation class. Therefore we nee to check, if we can add this configuration already.
     */
    if (null !== $GLOBALS['TCA']['pages']['types'][\PSB\PsbTemplates\Data\ExtensionInformation::DOKTYPES['MENU_DROPDOWN']]) {
        $GLOBALS['TCA']['pages']['types'][\PSB\PsbTemplates\Data\ExtensionInformation::DOKTYPES['MENU_DROPDOWN']] = array_merge($GLOBALS['TCA']['pages']['types'][\PSB\PsbTemplates\Data\ExtensionInformation::DOKTYPES['MENU_DROPDOWN']],
            ['columnsOverrides' => $columnsOverrides]);
    }
})();

