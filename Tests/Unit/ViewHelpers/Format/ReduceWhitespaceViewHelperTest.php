<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Format;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PSBits\FrontendTemplates\ViewHelpers\Format\ReduceWhitespaceViewHelper;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ReduceWhitespaceViewHelperTest
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Format
 */
class ReduceWhitespaceViewHelperTest extends UnitTestCase
{
    private ReduceWhitespaceViewHelper    $subject;
    private RenderingContext&MockObject   $renderingContext;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject          = new ReduceWhitespaceViewHelper();
        $this->renderingContext = $this->createMock(RenderingContext::class);
        $this->subject->setRenderingContext($this->renderingContext);
        $this->subject->initializeArguments();
    }

    #[Test]
    public function lineEndingsAreNormalized(): void
    {
        self::assertSame("line1\nline2\nline3", $this->render(['value' => "line1\r\nline2\r\n\r\n\r\nline3  "]));
    }

    #[Test]
    public function repeatedHorizontalWhitespaceIsCollapsed(): void
    {
        self::assertSame('one two three', $this->render(['value' => 'one    two  three']));
    }

    #[Test]
    public function indentationAroundLinebreaksIsRemoved(): void
    {
        self::assertSame("indented\ntext", $this->render(['value' => "  indented\n    text  "]));
    }

    #[Test]
    public function withReplaceLinebreaksAllLinebreaksBecomeSingleSpaces(): void
    {
        self::assertSame(
            'one two three',
            $this->render(['value' => "one\r\ntwo\n\nthree", 'replaceLinebreaks' => true])
        );
    }

    #[Test]
    public function withoutAValueTheChildContentIsUsed(): void
    {
        self::assertSame("child\ncontent", $this->render([], "child\ncontent"));
    }

    /**
     * @param array<string, mixed> $arguments
     */
    private function render(array $arguments, string $children = ''): mixed
    {
        // prepareArguments() returns ArgumentDefinition objects, not values, so the defaults have
        // to be unwrapped the way ViewHelperNode::evaluate() does before render() is called.
        // Without them every optional argument would be an undefined array key inside render().
        $defaults = [];

        foreach ($this->subject->prepareArguments() as $name => $definition) {
            $defaults[$name] = $definition->getDefaultValue();
        }

        $this->subject->setArguments($arguments + $defaults);
        $this->subject->setRenderChildrenClosure(static fn() => $children);

        return $this->subject->render();
    }
}
