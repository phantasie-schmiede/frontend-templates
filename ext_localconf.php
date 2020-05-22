<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
defined('TYPO3_MODE') or die();

(static function () {
    $extensionInformation = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\PSB\PsbTemplates\Data\ExtensionInformation::class);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
        <INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extensionInformation->getExtensionKey() . '/Configuration/TSConfig/PageTS.tsconfig">
    ');
})();
