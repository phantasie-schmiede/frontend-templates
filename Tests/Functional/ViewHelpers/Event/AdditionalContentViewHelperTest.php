<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Functional\ViewHelpers\Event;

use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Event\AdditionalContentEventInterface;
use PSBits\FrontendTemplates\Tests\Functional\Fixtures\AdditionalContentEventFixture;
use PSBits\Qa\Testing\Fixtures\InMemoryListenerProvider;
use PSBits\FrontendTemplates\ViewHelpers\Event\AdditionalContentViewHelper;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Class AdditionalContentViewHelperTest
 *
 * Dispatches the event against the real PSR-14 dispatcher with a registered listener, so the listener wiring runs
 * for real.
 *
 * @package PSBits\FrontendTemplates\Tests\Functional\ViewHelpers\Event
 */
#[IgnoreDeprecations]
class AdditionalContentViewHelperTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3/sysext/fluid_styled_content',
        'typo3conf/ext/friendsoftypo3/content-blocks',
        'typo3conf/ext/psbits/foundation',
        'typo3conf/ext/psbits/frontend-templates',
        'typo3conf/ext/psbits/view-helpers',
        'typo3conf/ext/wapplersystems/ws-scss',
    ];
    private ServerRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new ServerRequest(
            'http://example.com/',
            'GET',
            null,
            [],
            [
                'HTTP_HOST'   => 'example.com',
                'REQUEST_URI' => '/',
            ]
        );
        $GLOBALS['TYPO3_REQUEST'] = $this->request;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        parent::tearDown();
    }

    #[Test]
    public function aRegisteredListenerProvidesTheAdditionalContent(): void
    {
        $listenerProvider = new InMemoryListenerProvider();
        $listenerProvider->addListener(
            AdditionalContentEventFixture::class,
            static function (AdditionalContentEventFixture $event): void {
                $event->addContent('<p>Added by listener</p>');
            }
        );
        $eventDispatcher = new EventDispatcher($listenerProvider);

        $viewHelper = new AdditionalContentViewHelper($eventDispatcher);
        $viewHelper->setRenderingContext(
            GeneralUtility::makeInstance(RenderingContextFactory::class)
                ->create()
        );
        $viewHelper->initializeArguments();

        $defaults = [];

        foreach ($viewHelper->prepareArguments() as $name => $definition) {
            $defaults[$name] = $definition->getDefaultValue();
        }
        $viewHelper->setArguments([
            'eventClass'  => AdditionalContentEventFixture::class,
            'parameters'  => ['key' => 'value'],
        ] + $defaults);
        $viewHelper->setRenderChildrenClosure(static fn() => null);

        self::assertSame('<p>Added by listener</p>', (string)$viewHelper->render());
    }

    #[Test]
    public function theEventGetsTheRequest(): void
    {
        $capturedRequest = null;

        $listenerProvider = new InMemoryListenerProvider();
        $listenerProvider->addListener(
            AdditionalContentEventFixture::class,
            static function (AdditionalContentEventInterface $event) use (&$capturedRequest): void {
                $capturedRequest = $event->getRequest();
                $event->addContent('content');
            }
        );
        $eventDispatcher = new EventDispatcher($listenerProvider);

        $viewHelper = new AdditionalContentViewHelper($eventDispatcher);
        $context    = GeneralUtility::makeInstance(RenderingContextFactory::class)
            ->create();

        /*
         * The request is exposed as a rendering context attribute; older Fluid versions
         * set it via setAttribute(), newer ones via set().
         */
        if (method_exists($context, 'set')) {
            $context->set(ServerRequestInterface::class, $this->request);
        } else {
            $context->setAttribute(ServerRequestInterface::class, $this->request);
        }
        $viewHelper->setRenderingContext($context);
        $viewHelper->initializeArguments();

        $defaults = [];

        foreach ($viewHelper->prepareArguments() as $name => $definition) {
            $defaults[$name] = $definition->getDefaultValue();
        }
        $viewHelper->setArguments(['eventClass' => AdditionalContentEventFixture::class] + $defaults);
        $viewHelper->setRenderChildrenClosure(static fn() => null);
        $viewHelper->render();

        self::assertSame($this->request, $capturedRequest);
    }
}
