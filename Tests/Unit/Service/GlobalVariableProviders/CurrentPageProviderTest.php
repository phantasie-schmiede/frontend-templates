<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\Service\GlobalVariableProviders;

use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Service\GlobalVariableProviders\CurrentPageProvider;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class CurrentPageProviderTest
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\Service\GlobalVariableProviders
 */
class CurrentPageProviderTest extends UnitTestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        parent::tearDown();
    }

    #[Test]
    public function theCurrentPageRecordIsReturned(): void
    {
        $pageRecord = ['uid' => 42, 'title' => 'Test page'];

        $pageInformation = new PageInformation();
        $pageInformation->setPageRecord($pageRecord);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttribute')
            ->with('frontend.page.information')
            ->willReturn($pageInformation);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        self::assertSame($pageRecord, (new CurrentPageProvider())->getGlobalVariables());
    }
}
