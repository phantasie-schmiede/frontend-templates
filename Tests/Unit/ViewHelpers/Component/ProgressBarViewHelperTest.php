<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Component;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PSBits\FrontendTemplates\Data\ExtensionInformation;
use PSBits\FrontendTemplates\Service\ViewService;
use PSBits\FrontendTemplates\ViewHelpers\Component\ProgressBarViewHelper;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * The standalone view is stubbed, so the tests lock in the percentage calculation, the color thresholds and the
 * view assignment. The real template rendering is covered by the functional ViewServiceTest.
 */
class ProgressBarViewHelperTest extends UnitTestCase
{
    private array                    $assignedViewValues = [];
    private ProgressBarViewHelper    $subject;
    private ViewInterface&MockObject $view;
    private ViewService&MockObject   $viewService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->viewService = $this->createMock(ViewService::class);
        $this->view        = $this->createMock(ViewInterface::class);
        $this->viewService->method('createStandaloneView')
            ->willReturn($this->view);

        $this->view->method('assignMultiple')
            ->willReturnCallback(function(array $values): ViewInterface {
                $this->assignedViewValues = $values;

                return $this->view;
            });
        $this->view->method('render')
            ->willReturn('rendered');

        $this->subject = new ProgressBarViewHelper(new ExtensionInformation(), $this->viewService);
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
    public function aCustomRangeIsRespected(): void
    {
        $this->render(['maxValue' => 110, 'minValue' => 10, 'value' => 60]);

        self::assertSame(50.0, $this->assignedViewValues['percentage']);
    }

    #[Test]
    public function aNonPositiveRangeIsReported(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1758896064);

        $this->render(['maxValue' => 10, 'minValue' => 10, 'value' => 1]);
    }

    #[Test]
    public function providedClassesOverrideTheColorClasses(): void
    {
        $this->render(['classes' => 'custom', 'value' => 50]);

        self::assertSame('custom', $this->assignedViewValues['classes']);
    }

    #[Test]
    public function theChildContentIsPassedToTheTemplate(): void
    {
        $this->render(['value' => 50], '42 of 100');

        self::assertSame('42 of 100', $this->assignedViewValues['content']);
    }

    #[Test]
    public function theColorClassFollowsThePercentageThresholds(): void
    {
        $expected = [
            0   => 'bg-danger progress-bar-striped',
            10  => 'bg-danger progress-bar-striped',
            24  => 'bg-danger progress-bar-striped',
            25  => 'bg-warning progress-bar-striped',
            74  => 'bg-warning progress-bar-striped',
            75  => 'bg-success progress-bar-striped',
            100 => 'bg-success',
        ];

        foreach ($expected as $value => $classes) {
            $this->render(['value' => $value]);

            self::assertSame($classes, $this->assignedViewValues['classes'], "value {$value}");
        }
    }

    #[Test]
    public function thePercentageIsCalculatedFromTheValueRange(): void
    {
        $rendered = $this->render(['value' => 50]);

        self::assertSame('rendered', $rendered);
        self::assertSame(50.0, $this->assignedViewValues['percentage']);
        self::assertSame('bg-warning progress-bar-striped', $this->assignedViewValues['classes']);
    }

    /**
     * @param array<string, mixed> $arguments
     */
    private function render(array $arguments, string $children = ''): mixed
    {
        $defaults = [];

        foreach ($this->subject->prepareArguments() as $name => $definition) {
            $defaults[$name] = $definition->getDefaultValue();
        }

        $this->subject->setArguments($arguments + $defaults);
        $this->subject->setRenderChildrenClosure(static fn() => $children);

        return $this->subject->render();
    }
}
