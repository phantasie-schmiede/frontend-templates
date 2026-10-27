<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\Utility;

use PHPUnit\Framework\Attributes\Test;
use PSBits\Foundation\Utility\Typo3VersionUtility;
use PSBits\FrontendTemplates\Tests\Unit\Fixtures\FakeLanguageServiceFactory;
use PSBits\FrontendTemplates\Utility\StringUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class StringUtilityTest
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\Utility
 */
class StringUtilityTest extends UnitTestCase
{
    // The language service factory and the locales are pre-seeded singletons, which must not leak between tests.
    protected bool $resetSingletonInstances = true;

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * The enumeration separator and conjunction come from the extension language file, which the core
         * language stack cannot load in unit tests. A pre-seeded language service factory serves them instead.
         */
        $languageService = $this->createMock(LanguageService::class);
        $languageService->method('getLocale')
            ->willReturn(new Locale('en'));

        $labels = static function (string $key): string {
            $position = strrpos($key, ':');
            $key      = false === $position ? $key : substr($key, $position + 1);

            return match ($key) {
                'separator'   => ', ',
                'conjunction' => 'and',
                default       => $key,
            };
        };
        $languageService->method('sL')
            ->willReturnCallback($labels);
        $languageService->method('translate')
            ->willReturnCallback($labels);

        GeneralUtility::setSingletonInstance(
            LanguageServiceFactory::class,
            new FakeLanguageServiceFactory($languageService)
        );

        /*
         * On v13, the core keeps the language service in the runtime cache between lookups, which is not configured
         * in unit tests. A stubbed cache manager serves the pre-seeded language service instead.
         */
        if (false === Typo3VersionUtility::isAtLeast('14.0')) {
            $cache = $this->createMock(FrontendInterface::class);
            $cache->method('get')
                ->willReturn($languageService);

            $cacheManager = $this->createMock(CacheManager::class);
            $cacheManager->method('getCache')
                ->willReturn($cache);

            GeneralUtility::setSingletonInstance(CacheManager::class, $cacheManager);
        }
    }

    #[Test]
    public function anEmptyArrayYieldsAnEmptyString(): void
    {
        self::assertSame('', StringUtility::createEnumeration([]));
    }

    #[Test]
    public function aSingleItemIsReturnedAsIs(): void
    {
        self::assertSame('one', StringUtility::createEnumeration(['one']));
    }

    #[Test]
    public function twoItemsAreJoinedByTheConjunction(): void
    {
        self::assertSame('one and two', StringUtility::createEnumeration(['one', 'two']));
    }

    #[Test]
    public function threeItemsAreJoinedBySeparatorAndConjunction(): void
    {
        self::assertSame('one, two and three', StringUtility::createEnumeration(['one', 'two', 'three']));
    }

    #[Test]
    public function fourItemsAreJoinedBySeparatorAndConjunction(): void
    {
        self::assertSame('one, two, three and four', StringUtility::createEnumeration(['one', 'two', 'three', 'four']));
    }
}
