<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Event;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PSBits\FrontendTemplates\Event\AdditionalContentEventInterface;
use PSBits\FrontendTemplates\Tests\Unit\Fixtures\AdditionalContentEventFixture;
use PSBits\FrontendTemplates\ViewHelpers\Event\AdditionalContentViewHelper;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class AdditionalContentViewHelperTest
 *
 * The event dispatcher is stubbed, so the tests lock in the event validation and wiring. The dispatch against a real
 * PSR-14 dispatcher is covered by the functional test.
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Event
 */
class AdditionalContentViewHelperTest extends UnitTestCase
{
    private AdditionalContentViewHelper           $subject;
    private RenderingContext&MockObject           $renderingContext;
    private EventDispatcher&MockObject            $eventDispatcher;
    private ?AdditionalContentEventInterface      $dispatchedEvent = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->eventDispatcher->method('dispatch')
            ->willReturnCallback(function (object $event) {
                $this->dispatchedEvent = $event;
                $event->addContent('<p>Additional content</p>');

                return $event;
            });

        $this->subject          = new AdditionalContentViewHelper($this->eventDispatcher);
        $this->renderingContext = $this->createMock(RenderingContext::class);
        $this->subject->setRenderingContext($this->renderingContext);
        $this->subject->initializeArguments();
    }

    #[Test]
    public function aMissingEventClassIsReported(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1721043807);

        $this->render(['eventClass' => 'PSBits\\FrontendTemplates\\DoesNotExist']);
    }

    #[Test]
    public function aClassWithoutTheEventInterfaceIsReported(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1721040401);

        $this->render(['eventClass' => stdClass::class]);
    }

    #[Test]
    public function theDispatchedEventProvidesTheContent(): void
    {
        self::assertSame(
            '<p>Additional content</p>',
            $this->render(['eventClass' => AdditionalContentEventFixture::class])
        );
        self::assertInstanceOf(AdditionalContentEventFixture::class, $this->dispatchedEvent);
    }

    #[Test]
    public function parametersAndTheRequestArePassedToTheEvent(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $this->renderingContext->method('hasAttribute')
            ->with(ServerRequestInterface::class)
            ->willReturn(true);
        $this->renderingContext->method('getAttribute')
            ->with(ServerRequestInterface::class)
            ->willReturn($request);

        $this->render([
            'eventClass'  => AdditionalContentEventFixture::class,
            'parameters'  => ['key' => 'value'],
        ]);

        self::assertSame(['key' => 'value'], $this->dispatchedEvent->getParameters());
        self::assertSame($request, $this->dispatchedEvent->getRequest());
    }

    #[Test]
    public function withoutARequestTheEventGetsNull(): void
    {
        $this->renderingContext->method('hasAttribute')
            ->willReturn(false);

        $this->render(['eventClass' => AdditionalContentEventFixture::class]);

        self::assertNull($this->dispatchedEvent->getRequest());
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
