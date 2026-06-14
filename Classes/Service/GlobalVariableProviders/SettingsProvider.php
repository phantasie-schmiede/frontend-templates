<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Service\GlobalVariableProviders;

use PSBits\Foundation\Service\GlobalVariableProviders\AbstractProvider;
use PSBits\Foundation\Utility\ContextUtility;
use RuntimeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageInformation;

use function array_key_exists;
use function in_array;

/**
 * Class SettingsProvider
 *
 * @package PSBits\Templates\Service\GlobalVariableProviders
 */
class SettingsProvider extends AbstractProvider
{
    /** @var array<string, class-string<SettingSourceInterface>> */
    protected static array $sources = [];

    public static function registerSource(string $source, string $identifier = ''): void
    {
        if (!is_subclass_of($source, SettingSourceInterface::class)) {
            throw new RuntimeException(
                __CLASS__ . ': The source "' . $source . '" does not implement the required SettingSourceInterface!',
                1773992928
            );
        }

        if (in_array($source, self::$sources, true)) {
            throw new RuntimeException(
                __CLASS__ . ': The source "' . $source . '" is already registered!',
                1773992985
            );
        }

        if (!empty($identifier) && array_key_exists($identifier, self::$sources)) {
            throw new RuntimeException(
                __CLASS__ . ': The identifier "' . $identifier . '" is already used for another source!',
                1773993176
            );
        }

        self::$sources[$identifier ?: $source] = $source;
    }

    /**
     * @return array<string, mixed>
     */
    public function getGlobalVariables(): array
    {
        /** @var PageInformation $pageInformation */
        $pageInformation = ContextUtility::getRequest()
            ->getAttribute('frontend.page.information');

        /** @var array<string, SettingSourceInterface> $pendingSources */
        $pendingSources = [];

        /** @var array<int, array<string, mixed>> $rootline */
        $rootline = array_reverse($pageInformation->getLocalRootLine());
        $settings = [];

        foreach (self::$sources as $identifier => $source) {
            /** @var SettingSourceInterface $sourceInstance */
            $sourceInstance = GeneralUtility::makeInstance($source);
            $pendingSources[$identifier] = $sourceInstance;
        }

        foreach ($rootline as $page) {
            foreach ($pendingSources as $identifier => $sourceInstance) {
                $sourceSettings = $sourceInstance->findByPid($page['uid']);

                if (!empty($sourceSettings)) {
                    $settings[$identifier] = $sourceSettings;
                    unset($pendingSources[$identifier]);
                }
            }

            if (empty($pendingSources)) {
                break;
            }
        }

        return $settings;
    }
}
