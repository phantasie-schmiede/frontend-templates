<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Component;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PSBits\FrontendTemplates\Data\ExtensionInformation;
use PSBits\FrontendTemplates\Service\ViewService;
use PSBits\FrontendTemplates\ViewHelpers\Component\ButtonViewHelper;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ButtonViewHelperTest
 *
 * The standalone view is stubbed, so the tests lock in the attribute, class and data-attribute processing and the
 * view assignment. The real template rendering is covered by the functional ViewServiceTest.
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Component
 */
class ButtonViewHelperTest extends UnitTestCase
{
    private ButtonViewHelper              $subject;
    private ViewService&MockObject        $viewService;
    private ViewInterface&MockObject      $view;
    private array                         $assignedViewValues = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->viewService  = $this->createMock(ViewService::class);
        $this->view         = $this->createMock(ViewInterface::class);
        $this->viewService->method('createStandaloneView')
            ->willReturn($this->view);

        $this->view->method('assignMultiple')
            ->willReturnCallback(function (array $values): ViewInterface {
                $this->assignedViewValues = $values;

                return $this->view;
            });
        $this->view->method('render')
            ->willReturn('rendered');

        $this->subject = new ButtonViewHelper(new ExtensionInformation(), $this->viewService);
        $this->subject->setRenderingContext($this->createMock(RenderingContext::class));
        $this->subject->initializeArguments();

        // The view service requires the request of the rendering process, which always exists on frontend rendering.
        $GLOBALS['TYPO3_REQUEST'] = $this->createMock(ServerRequestInterface::class);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        parent::tearDown();
    }

    #[Test]
    public function aButtonRendersAsButtonElementWithTypeAttribute(): void
    {
        $this->render([]);

        self::assertSame('button', $this->assignedViewValues['tag']);
        self::assertSame(['type' => 'button'], $this->assignedViewValues['additionalAttributes']);
        self::assertSame('align-items-center btn d-flex', $this->assignedViewValues['classes']);
        self::assertFalse($this->assignedViewValues['showSpinner']);
        self::assertSame([], $this->assignedViewValues['data']);
    }

    #[Test]
    public function anHrefRendersALinkWithTheHrefAttribute(): void
    {
        $this->render(['href' => 'https://example.com']);

        self::assertSame('a', $this->assignedViewValues['tag']);
        self::assertSame(['href' => 'https://example.com'], $this->assignedViewValues['additionalAttributes']);
    }

    #[Test]
    public function anExternalLinkGetsTargetAndRelAttributes(): void
    {
        $this->render(['href' => 'https://example.com', 'externalLink' => true]);

        $attributes = $this->assignedViewValues['additionalAttributes'];

        self::assertSame('https://example.com', $attributes['href']);
        self::assertSame('noopener noreferrer', $attributes['rel']);
        self::assertSame('_blank', $attributes['target']);
    }

    #[Test]
    public function explicitRelAndTargetAttributesAreUsedForLinks(): void
    {
        $this->render(['href' => 'https://example.com', 'rel' => 'nofollow', 'target' => 'self']);

        $attributes = $this->assignedViewValues['additionalAttributes'];

        self::assertSame('nofollow', $attributes['rel']);
        self::assertSame('self', $attributes['target']);
    }

    #[Test]
    public function additionalClassesAreMergedDeduplicatedAndSorted(): void
    {
        $this->render(['additionalClasses' => 'custom btn']);

        self::assertSame('align-items-center btn custom d-flex', $this->assignedViewValues['classes']);
    }

    #[Test]
    public function additionalClassesMayBeProvidedAsAnArray(): void
    {
        $this->render(['additionalClasses' => ['custom']]);

        self::assertSame('align-items-center btn custom d-flex', $this->assignedViewValues['classes']);
    }

    #[Test]
    public function theSpinnerTextIsPassedAsDataAttributes(): void
    {
        $this->render(['spinnerText' => 'Saving']);

        self::assertTrue($this->assignedViewValues['showSpinner']);
        self::assertSame('', $this->assignedViewValues['data']['psbits-spinner-phase-out']);
        self::assertSame('Saving', $this->assignedViewValues['data']['psbits-spinner-text']);
    }

    #[Test]
    public function aHiddenSpinnerTextOnlyGetsThePhaseOutAttributeWithAnIcon(): void
    {
        $this->render(['spinnerTextHidden' => 'Saving', 'iconIdentifier' => 'actions-save']);

        self::assertTrue($this->assignedViewValues['showSpinner']);
        self::assertSame('Saving', $this->assignedViewValues['data']['psbits-spinner-text-hidden']);
        self::assertSame('', $this->assignedViewValues['data']['psbits-spinner-phase-out']);
    }

    #[Test]
    public function aHiddenSpinnerTextWithoutAnIconHasNoPhaseOutAttribute(): void
    {
        $this->render(['spinnerTextHidden' => 'Saving']);

        self::assertTrue($this->assignedViewValues['showSpinner']);
        self::assertArrayNotHasKey('psbits-spinner-phase-out', $this->assignedViewValues['data']);
    }

    #[Test]
    public function labelAndIconArePassedToTheTemplate(): void
    {
        $this->render(['label' => 'Save', 'iconIdentifier' => 'actions-save']);

        self::assertSame('Save', $this->assignedViewValues['label']);
        self::assertSame('actions-save', $this->assignedViewValues['iconIdentifier']);
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
