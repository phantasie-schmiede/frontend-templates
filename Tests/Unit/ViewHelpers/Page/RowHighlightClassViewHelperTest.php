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

class RowHighlightClassViewHelperTest extends UnitTestCase
{
    private RenderingContext&MockObject $renderingContext;
    private RowHighlightClassViewHelper $subject;

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
            $this->render(['currentPage' => ['tx_frontendtemplates_row_highlight_1' => 'emphasis'], 'row' => 1])
        );
        self::assertSame(
            'page-row-highlight-subtle',
            $this->render(['currentPage' => ['tx_frontendtemplates_row_highlight_2' => 'subtle'], 'row' => 2])
        );
        self::assertSame(
            'page-row-highlight-emphasis',
            $this->render(['currentPage' => ['tx_frontendtemplates_row_highlight_3' => 'emphasis'], 'row' => 3])
        );
    }

    #[Test]
    public function missingOrUnknownTokensYieldNoClass(): void
    {
        self::assertSame('', $this->render(['row' => 1]));
        self::assertSame(
            '',
            $this->render(['currentPage' => ['tx_frontendtemplates_row_highlight_1' => 'none'], 'row' => 1])
        );
        self::assertSame(
            '',
            $this->render(['currentPage' => ['tx_frontendtemplates_row_highlight_1' => 'glow'], 'row' => 1])
        );
    }

    #[Test]
    public function rowsWithoutATokenFieldYieldNoClass(): void
    {
        self::assertSame(
            '',
            $this->render(['currentPage' => ['tx_frontendtemplates_row_highlight_1' => 'emphasis'], 'row' => 0])
        );
        self::assertSame(
            '',
            $this->render(['currentPage' => ['tx_frontendtemplates_row_highlight_4' => 'emphasis'], 'row' => 4])
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
