<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PSBits\FrontendTemplates\ViewHelpers\SvgIconViewHelper;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconProvider\AbstractSvgIconProvider;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class SvgIconViewHelperTest
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\ViewHelpers
 */
class SvgIconViewHelperTest extends UnitTestCase
{
    private const string SVG = '<svg width="16" height="16" viewBox="0 0 16 16"><path d="M0 0h16v16H0z"/></svg>';

    private SvgIconViewHelper               $subject;
    private IconFactory&MockObject          $iconFactory;
    private RenderingContext&MockObject     $renderingContext;

    protected function setUp(): void
    {
        parent::setUp();

        $this->iconFactory        = $this->createMock(IconFactory::class);
        $this->subject            = new SvgIconViewHelper($this->iconFactory);
        $this->renderingContext   = $this->createMock(RenderingContext::class);
        $this->subject->setRenderingContext($this->renderingContext);
        $this->subject->initializeArguments();
    }

    #[Test]
    public function withoutCustomizationsTheMarkupIsReturnedAsIs(): void
    {
        $this->provideIcon(self::SVG);

        self::assertSame(self::SVG, $this->render(['identifier' => 'my-icon']));
    }

    #[Test]
    public function theWidthHeightAndViewBoxAreReplaced(): void
    {
        $this->provideIcon(self::SVG);

        self::assertSame(
            '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h16v16H0z"/></svg>',
            $this->render(['identifier' => 'my-icon', 'width' => 24, 'height' => 24, 'viewBox' => '0 0 24 24'])
        );
    }

    #[Test]
    public function additionalClassesAreAppendedToAnExistingClassAttribute(): void
    {
        $this->provideIcon('<svg class="icon-base" width="16" height="16"><path d="M0 0h16v16H0z"/></svg>');

        self::assertSame(
            '<svg class="icon-base icon-lg" width="16" height="16"><path d="M0 0h16v16H0z"/></svg>',
            $this->render(['identifier' => 'my-icon', 'additionalClasses' => 'icon-lg'])
        );
    }

    #[Test]
    public function additionalClassesAddAMissingClassAttribute(): void
    {
        $this->provideIcon(self::SVG);

        self::assertSame(
            '<svg class=" icon-lg" width="16" height="16" viewBox="0 0 16 16"><path d="M0 0h16v16H0z"/></svg>',
            $this->render(['identifier' => 'my-icon', 'additionalClasses' => 'icon-lg'])
        );
    }

    #[Test]
    public function additionalClassesMayBeProvidedAsAnArray(): void
    {
        $this->provideIcon('<svg class="icon-base" width="16" height="16"><path d="M0 0h16v16H0z"/></svg>');

        self::assertSame(
            '<svg class="icon-base icon-lg icon-xl" width="16" height="16"><path d="M0 0h16v16H0z"/></svg>',
            $this->render(['identifier' => 'my-icon', 'additionalClasses' => ['icon-lg', 'icon-xl']])
        );
    }

    /**
     * @param array<string, mixed> $arguments
     */
    private function render(array $arguments): string
    {
        $defaults = [];

        foreach ($this->subject->prepareArguments() as $name => $definition) {
            $defaults[$name] = $definition->getDefaultValue();
        }

        $this->subject->setArguments($arguments + $defaults);
        $this->subject->setRenderChildrenClosure(static fn() => null);

        return (string)$this->subject->render();
    }

    private function provideIcon(string $markup): void
    {
        $icon = $this->createMock(Icon::class);
        $icon->method('getMarkup')
            ->with(AbstractSvgIconProvider::MARKUP_IDENTIFIER_INLINE)
            ->willReturn($markup);

        $this->iconFactory->method('getIcon')
            ->willReturn($icon);
    }
}
