<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Service;

use JsonException;
use PSBits\Foundation\Data\ExtensionInformationInterface;
use PSBits\Foundation\Service\TypoScriptProviderService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class ViewService
 *
 * @package PSBits\Templates\Service
 */
readonly class ViewService
{
    public function __construct(
        protected TypoScriptProviderService $typoScriptProviderService,
        protected ViewFactoryInterface      $viewFactory,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     */
    public function createStandaloneView(
        ExtensionInformationInterface $extensionInformation,
        ServerRequestInterface        $request,
        string                        $templateIdentifier,
    ): ViewInterface {
        if (!str_contains($templateIdentifier, '.')) {
            $templateIdentifier .= '.html';
        }

        $viewSettings = $this->getViewSettings($extensionInformation);
        $viewFactoryData = new ViewFactoryData(
            partialRootPaths       : $viewSettings['partialRootPaths'] ?? [],
            layoutRootPaths        : $viewSettings['layoutRootPaths'] ?? [],
            templatePathAndFilename: ($viewSettings['templateRootPaths'] ?? []) . $templateIdentifier,
            request                : $request,
        );

        return $this->viewFactory->create($viewFactoryData);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     */
    private function getViewSettings(ExtensionInformationInterface $extensionInformation): array
    {
        return $this->typoScriptProviderService->get(
            'view',
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            $extensionInformation->getExtensionName(),
        );
    }
}
