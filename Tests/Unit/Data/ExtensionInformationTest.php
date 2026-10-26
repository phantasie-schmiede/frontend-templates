<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\Data;

use PHPUnit\Framework\TestCase;
use PSBits\Foundation\Data\PageTypeConfiguration;
use PSBits\FrontendTemplates\Data\ExtensionInformation;

final class ExtensionInformationTest extends TestCase
{
    public function testRegistersTheMenuDropdownPageType(): void
    {
        $extensionInformation = new ExtensionInformation();

        self::assertSame([], $extensionInformation->getMainModules());
        self::assertSame([], $extensionInformation->getModules());
        self::assertSame([], $extensionInformation->getPlugins());

        $pageTypes = $extensionInformation->getPageTypes();

        self::assertCount(1, $pageTypes);
        self::assertContainsOnlyInstancesOf(PageTypeConfiguration::class, $pageTypes);

        $menuDropdown = $pageTypes[0];
        self::assertSame('menuDropdown', $menuDropdown->getName());
        self::assertSame(100, $menuDropdown->getDoktype());
        self::assertSame(['pages'], $menuDropdown->getAllowedTables());
    }
}
