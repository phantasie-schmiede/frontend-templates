<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(static function (): void {
    $fields = [
        'tx_templates_row_highlight_1',
        'tx_templates_row_highlight_2',
        'tx_templates_row_highlight_3',
        'tx_templates_row_highlight_4',
    ];

    ExtensionManagementUtility::addToAllTCAtypes(
        'pages',
        '--div--;LLL:EXT:templates/Resources/Private/Language/Backend/Configuration/TCA/Overrides/page.xlf:rowHighlights.tab,'
        . implode(',', $fields),
        '',
        'after:--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance'
    );

    $displayConditionPrefix = 'USER:PSBits\\Templates\\Backend\\DisplayCondition\\PageRowHighlightDisplayCondition->matches:';
    $itemConfiguration = [
        ['LLL:EXT:templates/Resources/Private/Language/Backend/Configuration/TCA/Overrides/page.xlf:rowHighlight.none', 'none'],
        ['LLL:EXT:templates/Resources/Private/Language/Backend/Configuration/TCA/Overrides/page.xlf:rowHighlight.subtle', 'subtle'],
        ['LLL:EXT:templates/Resources/Private/Language/Backend/Configuration/TCA/Overrides/page.xlf:rowHighlight.emphasis', 'emphasis'],
    ];

    foreach ($fields as $index => $fieldName) {
        if (!isset($GLOBALS['TCA']['pages']['columns'][$fieldName])) {
            continue;
        }

        $GLOBALS['TCA']['pages']['columns'][$fieldName]['description'] = 'LLL:EXT:templates/Resources/Private/Language/Backend/Configuration/TCA/Overrides/page.xlf:rowHighlights.description';
        $GLOBALS['TCA']['pages']['columns'][$fieldName]['displayCond'] = $displayConditionPrefix . ($index + 1);
        $GLOBALS['TCA']['pages']['columns'][$fieldName]['config']['items'] = $itemConfiguration;
    }
});
