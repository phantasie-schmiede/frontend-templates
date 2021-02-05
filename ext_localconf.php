<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
defined('TYPO3_MODE') or die();

(static function () {
    \PSB\PsbFoundation\Service\GlobalVariableService::registerGlobalVariableProvider(\PSB\PsbTemplates\Service\GlobalVariableProviders\FrontendUserProvider::class);

    $extensionInformation = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\PSB\PsbTemplates\Data\ExtensionInformation::class);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
        @import "EXT:' . $extensionInformation->getExtensionKey() . '/Configuration/TSConfig/PageTS.tsconfig"
    ');
})();
