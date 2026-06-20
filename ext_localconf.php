<?php
declare(strict_types=1);

use PSBits\Foundation\Service\GlobalVariableService;
use PSBits\Templates\Service\GlobalVariableProviders\CurrentPageProvider;
use PSBits\Templates\Domain\Repository\Settings\HeaderButtonRepository;
use PSBits\Templates\Service\GlobalVariableProviders\SettingsProvider;

defined('TYPO3') or die();

(static function() {
    GlobalVariableService::registerGlobalVariableProvider(CurrentPageProvider::class);
    GlobalVariableService::registerGlobalVariableProvider(SettingsProvider::class);
    SettingsProvider::registerSource(HeaderButtonRepository::class, 'headerButtons');
})();
