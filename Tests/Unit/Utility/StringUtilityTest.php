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
use PSBits\FrontendTemplates\Utility\StringUtility;
use PSBits\Qa\Testing\Support\LanguageStackSeeder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class StringUtilityTest
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\Utility
 */
class StringUtilityTest extends UnitTestCase
{
    use LanguageStackSeeder;

    // The language service factory and the locales are pre-seeded singletons, which must not leak between tests.
    protected bool $resetSingletonInstances = true;

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * The enumeration separator and conjunction come from the extension language file, which the core
         * language stack cannot load in unit tests. The seeder pre-seeds a language service factory that
         * serves them instead.
         */
        $labels = static function (string $key): string {
            $position = strrpos($key, ':');
            $key      = false === $position ? $key : substr($key, $position + 1);

            return match ($key) {
                'separator'   => ', ',
                'conjunction' => 'and',
                default       => $key,
            };
        };
        $this->seedLanguageStack($labels);
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
