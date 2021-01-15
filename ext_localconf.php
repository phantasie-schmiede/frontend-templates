<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
defined('TYPO3_MODE') or die();

(static function () {
    $frontendUserProvider = \PSB\PsbFoundation\Utility\ObjectUtility::get(\PSB\PsbTemplates\Service\GlobalVariableProviders\FrontendUserProvider::class);
    \PSB\PsbFoundation\Service\GlobalVariableService::registerGlobalVariableProvider($frontendUserProvider);

    $extensionInformation = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\PSB\PsbTemplates\Data\ExtensionInformation::class);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
        @import "EXT:' . $extensionInformation->getExtensionKey() . '/Configuration/TSConfig/PageTS.tsconfig"
    ');
})();
