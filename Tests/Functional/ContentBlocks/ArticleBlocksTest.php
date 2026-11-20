<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Functional\ContentBlocks;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ArticleBlocksTest extends FunctionalTestCase
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
    public function blocksAndRequiredFactFieldsAreRegistered(): void
    {
        self::assertArrayHasKey('psbits_infobox', $GLOBALS['TCA']['tt_content']['types']);
        self::assertArrayHasKey('psbits_quote', $GLOBALS['TCA']['tt_content']['types']);
        self::assertTrue($GLOBALS['TCA']['tx_frontendtemplates_facts']['columns']['label']['config']['required']);
        self::assertTrue($GLOBALS['TCA']['tx_frontendtemplates_facts']['columns']['value']['config']['required']);
    }

    #[Test]
    public function factsEscapeMarkupAndRetainLineBreaks(): void
    {
        $html = $this->renderBlock('infobox', [
            'facts'         => [['label' => '<img>', 'value' => "First line\nSecond line"]],
            'header_layout' => 100,
            'info_variant'  => 'information',
        ]);

        self::assertStringContainsString('&lt;img&gt;', $html);
        self::assertStringContainsString('<br', $html);
        self::assertStringContainsString('<dl', $html);
        self::assertStringContainsString('<dt>', $html);
        self::assertStringContainsString('<dd>', $html);
        self::assertStringNotContainsString('<a ', $html);
    }

    #[Test]
    public function quotationEscapesTextAndRendersAttribution(): void
    {
        $html = $this->renderBlock('quote', [
            'header_layout' => 100,
            'quote_author'  => 'A & B',
            'quote_source'  => 'Source',
            'quote_text'    => '<script>alert(1)</script>',
        ]);

        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('&lt;script&gt;', $html);
        self::assertStringContainsString('<blockquote', $html);
        self::assertStringContainsString('<cite>Source</cite>', $html);
    }

    #[Test]
    public function quotationWithoutAttributionHasNoEmptyCaption(): void
    {
        $html = $this->renderBlock('quote', [
            'header_layout' => 100,
            'quote_text'    => 'Only the quotation',
        ]);

        self::assertStringNotContainsString('<figcaption', $html);
    }

    private function renderBlock(string $block, array $data): string
    {
        $view = $this->get(ViewFactoryInterface::class)->create(new ViewFactoryData(
            templatePathAndFilename: GeneralUtility::getFileAbsFileName(
                'EXT:frontend_templates/ContentBlocks/ContentElements/' . $block . '/templates/frontend.html'
            ),
        ));
        $view->assign('data', $data);

        return $view->render('frontend');
    }
}
