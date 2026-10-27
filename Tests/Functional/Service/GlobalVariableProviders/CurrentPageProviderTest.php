<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Functional\Service\GlobalVariableProviders;

use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Service\GlobalVariableProviders\CurrentPageProvider;
use PSBits\FrontendTemplates\Tests\Functional\Fixtures\FunctionalRequestFactory;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Class CurrentPageProviderTest
 *
 * @package PSBits\FrontendTemplates\Tests\Functional\Service\GlobalVariableProviders
 */
#[IgnoreDeprecations]
class CurrentPageProviderTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3/sysext/fluid_styled_content',
        'typo3conf/ext/friendsoftypo3/content-blocks',
        'typo3conf/ext/psbits/foundation',
        'typo3conf/ext/psbits/frontend-templates',
        'typo3conf/ext/psbits/view-helpers',
        'typo3conf/ext/wapplersystems/ws-scss',
    ];

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        parent::tearDown();
    }

    #[Test]
    public function theCurrentPageRecordIsProvided(): void
    {
        $pageRecord = ['uid' => 42, 'title' => 'Test page'];

        $pageInformation = new PageInformation();
        $pageInformation->setPageRecord($pageRecord);

        $GLOBALS['TYPO3_REQUEST'] = FunctionalRequestFactory::createFrontendRequest([
            'frontend.page.information' => $pageInformation,
        ]);

        self::assertSame($pageRecord, (new CurrentPageProvider())->getGlobalVariables());
    }
}
