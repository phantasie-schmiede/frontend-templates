<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Page;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PSBits\FrontendTemplates\ViewHelpers\Page\RowHighlightClassViewHelper;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class RowHighlightClassViewHelperTest
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Page
 */
class RowHighlightClassViewHelperTest extends UnitTestCase
{
    private RowHighlightClassViewHelper   $subject;
    private RenderingContext&MockObject   $renderingContext;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject          = new RowHighlightClassViewHelper();
        $this->renderingContext = $this->createMock(RenderingContext::class);
        $this->subject->setRenderingContext($this->renderingContext);
        $this->subject->initializeArguments();
    }

    #[Test]
    public function knownTokensAreMappedToTheirClasses(): void
    {
        self::assertSame(
            'page-row-highlight-emphasis',
            $this->render(['row' => 1, 'currentPage' => ['tx_templates_row_highlight_1' => 'emphasis']])
        );
        self::assertSame(
            'page-row-highlight-subtle',
            $this->render(['row' => 2, 'currentPage' => ['tx_templates_row_highlight_2' => 'subtle']])
        );
        self::assertSame(
            'page-row-highlight-emphasis',
            $this->render(['row' => 3, 'currentPage' => ['tx_templates_row_highlight_3' => 'emphasis']])
        );
    }

    #[Test]
    public function missingOrUnknownTokensYieldNoClass(): void
    {
        self::assertSame('', $this->render(['row' => 1]));
        self::assertSame(
            '',
            $this->render(['row' => 1, 'currentPage' => ['tx_templates_row_highlight_1' => 'none']])
        );
        self::assertSame(
            '',
            $this->render(['row' => 1, 'currentPage' => ['tx_templates_row_highlight_1' => 'glow']])
        );
    }

    #[Test]
    public function rowsWithoutATokenFieldYieldNoClass(): void
    {
        self::assertSame(
            '',
            $this->render(['row' => 0, 'currentPage' => ['tx_templates_row_highlight_1' => 'emphasis']])
        );
        self::assertSame(
            '',
            $this->render(['row' => 4, 'currentPage' => ['tx_templates_row_highlight_4' => 'emphasis']])
        );
    }

    /**
     * @param array<string, mixed> $arguments
     */
    private function render(array $arguments): mixed
    {
        $defaults = [];

        foreach ($this->subject->prepareArguments() as $name => $definition) {
            $defaults[$name] = $definition->getDefaultValue();
        }

        $this->subject->setArguments($arguments + $defaults);
        $this->subject->setRenderChildrenClosure(static fn() => null);

        return $this->subject->render();
    }
}
