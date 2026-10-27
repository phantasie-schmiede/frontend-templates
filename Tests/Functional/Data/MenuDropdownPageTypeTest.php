<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Functional\Data;

use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Data\ExtensionInformation;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Class MenuDropdownPageTypeTest
 *
 * The extension registers the menu dropdown page type on boot; this test guards the registration against the
 * actually built TCA.
 *
 * @package PSBits\FrontendTemplates\Tests\Functional\Data
 */
class MenuDropdownPageTypeTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3/sysext/fluid_styled_content',
        'typo3conf/ext/friendsoftypo3/content-blocks',
        'typo3conf/ext/psbits/foundation',
        'typo3conf/ext/psbits/frontend-templates',
        'typo3conf/ext/psbits/view-helpers',
        'typo3conf/ext/wapplersystems/ws-scss',
    ];

    #[Test]
    public function theMenuDropdownPageTypeIsRegisteredInThePagesTca(): void
    {
        /*
         * On v14 the TCA build normalizes select items to the associative label/value format; on v13 the
         * added items keep the legacy list format, so both shapes are extracted.
         */
        $items  = $GLOBALS['TCA']['pages']['columns']['doktype']['config']['items'];
        $values = array_map(
            static fn(array $item) => $item['value'] ?? $item[1] ?? null,
            $items
        );

        self::assertContains(
            ExtensionInformation::DOKTYPES['MENU_DROPDOWN'],
            $values,
            'The menu dropdown doktype is not registered in the pages TCA.'
        );
        self::assertArrayHasKey(
            ExtensionInformation::DOKTYPES['MENU_DROPDOWN'],
            $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'],
            'The menu dropdown page type has no typeicon configured.'
        );
        self::assertArrayHasKey(
            ExtensionInformation::DOKTYPES['MENU_DROPDOWN'],
            $GLOBALS['TCA']['pages']['types'],
            'The menu dropdown page type has no types entry configured.'
        );
    }
}
