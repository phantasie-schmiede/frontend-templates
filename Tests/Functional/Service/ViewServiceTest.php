<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Functional\Service;

use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Data\ExtensionInformation;
use PSBits\FrontendTemplates\Service\ViewService;
use PSBits\FrontendTemplates\Tests\Functional\Fixtures\FunctionalRequestFactory;
use PSBits\FrontendTemplates\ViewHelpers\Component\ButtonViewHelper;
use PSBits\FrontendTemplates\ViewHelpers\Component\ProgressBarViewHelper;
use PSBits\Foundation\Service\TypoScriptProviderService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Class ViewServiceTest
 *
 * Renders the component view helpers through the real view service, view factory and templates, so the template
 * wiring (namespaces, partials, view settings) runs for real.
 *
 * @package PSBits\FrontendTemplates\Tests\Functional\Service
 */
#[IgnoreDeprecations]
class ViewServiceTest extends FunctionalTestCase
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

        $this->bootstrapRequest();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        parent::tearDown();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function theProgressBarIsRenderedFromItsTemplate(): void
    {
        $viewHelper = new ProgressBarViewHelper(
            GeneralUtility::makeInstance(ExtensionInformation::class),
            $this->createViewService(),
        );

        $output = $this->renderViewHelper($viewHelper, ['value' => 50]);

        self::assertStringContainsString('class="progress-bar bg-warning progress-bar-striped"', $output);
        self::assertStringContainsString('style="width: 50%"', $output);
        self::assertStringContainsString('aria-valuemax="100"', $output);
        self::assertStringContainsString('aria-valuenow="50"', $output);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function theButtonIsRenderedAsButtonElement(): void
    {
        $viewHelper = new ButtonViewHelper(
            GeneralUtility::makeInstance(ExtensionInformation::class),
            $this->createViewService(),
        );

        $output = $this->renderViewHelper($viewHelper, ['label' => 'Save']);

        self::assertStringContainsString('<button', $output);
        self::assertStringContainsString('type="button"', $output);
        self::assertStringContainsString('class="align-items-center btn d-flex"', $output);
        self::assertStringContainsString('Save', $output);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function theButtonIsRenderedAsAnExternalLink(): void
    {
        $viewHelper = new ButtonViewHelper(
            GeneralUtility::makeInstance(ExtensionInformation::class),
            $this->createViewService(),
        );

        $output = $this->renderViewHelper(
            $viewHelper,
            [
                'label'        => 'GitHub',
                'href'         => 'https://github.com',
                'externalLink' => true,
            ]
        );

        self::assertStringContainsString('<a', $output);
        self::assertStringContainsString('href="https://github.com"', $output);
        self::assertStringContainsString('rel="noopener noreferrer"', $output);
        self::assertStringContainsString('target="_blank"', $output);
        self::assertStringContainsString('GitHub', $output);
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function renderViewHelper(AbstractViewHelper $viewHelper, array $arguments): string
    {
        $context = GeneralUtility::makeInstance(RenderingContextFactory::class)
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

        $viewHelper->setArguments($arguments + $defaults);
        $viewHelper->setRenderChildrenClosure(static fn() => null);

        return (string)$viewHelper->render();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createViewService(): ViewService
    {
        /*
         * The view service is a private container service that gets inlined when the container is compiled, so it
         * cannot be retrieved by id; its two dependencies are public and can be instantiated directly.
         */
        return new ViewService(
            GeneralUtility::makeInstance(TypoScriptProviderService::class),
            GeneralUtility::makeInstance(ViewFactoryInterface::class),
        );
    }

    private function bootstrapRequest(): void
    {
        $this->request = FunctionalRequestFactory::createFrontendRequest();

        $GLOBALS['TYPO3_REQUEST'] = $this->request;
    }
}
