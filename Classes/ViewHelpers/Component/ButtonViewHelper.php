<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\ViewHelpers\Component;

use JsonException;
use PSBits\Foundation\Utility\ContextUtility;
use PSBits\FrontendTemplates\Data\ExtensionInformation;
use PSBits\FrontendTemplates\Service\ViewService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

use function is_array;
use function is_string;

/**
 * Class ButtonViewHelper
 *
 * Renders a button with optional icon and spinner text. The button renders as button element by default, but can be
 * rendered as a link by setting the href argument. If the externalLink argument is set to true, the button will be
 * rendered as a link with target="_blank" and rel="noopener noreferrer".
 *
 * @package PSBits\FrontendTemplates\ViewHelpers\Component
 */
class ButtonViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function __construct(
        protected readonly ExtensionInformation $extensionInformation,
        protected readonly ViewService          $viewService,
    ) {
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('additionalAttributes', 'array', 'attributes added to the tag', false, []);
        $this->registerArgument('additionalClasses', 'mixed', 'additional CSS classes as string or array');
        $this->registerArgument('data', 'array', 'data attributes added to the tag', false, []);
        $this->registerArgument(
            'externalLink',
            'bool',
            'sets target to _blank and rel to "noopener noreferrer", if href is set',
            false,
            false
        );
        $this->registerArgument('href', 'string', 'href attribute, sets tag to "a"');
        $this->registerArgument('iconIdentifier', 'string', 'optional icon prepending the label');
        $this->registerArgument('label', 'string', 'the button label');
        $this->registerArgument('rel', 'string', 'the rel attribute if tag is set to "a"');
        $this->registerArgument('spinnerText', 'string', 'spinner text shown while processing instead of the label');
        $this->registerArgument('spinnerTextHidden', 'string', 'spinner text for screen readers only');
        $this->registerArgument('tag', 'string', 'tag name, can be "a" or "button"', false, 'button');
        $this->registerArgument('target', 'string', 'the target attribute if tag is set to "a"');
        $this->registerArgument('type', 'string', 'type attribute if tag is set to "button"', false, 'button');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     */
    public function render(): string
    {
        /** @var array<string, mixed> $additionalAttributes */
        $additionalAttributes = $this->arguments['additionalAttributes'];

        /** @var array<string, mixed> $dataAttributes */
        $dataAttributes  = $this->arguments['data'];
        $argumentClasses = $this->arguments['additionalClasses'];
        $classes         = [];

        if (is_string($argumentClasses)) {
            $classes = GeneralUtility::trimExplode(' ', $argumentClasses, true);
        } elseif (is_array($argumentClasses)) {
            $classes = $argumentClasses;
        }

        $this->processAdditionalAttributes($additionalAttributes);
        $this->processClasses($classes);
        $this->processDataAttributes($dataAttributes);

        $view = $this->viewService->createStandaloneView(
            $this->extensionInformation,
            ContextUtility::getRequest(),
            'ViewHelpers/Component/Button'
        );
        $view->assignMultiple([
            'additionalAttributes' => $additionalAttributes,
            'classes'              => implode(' ', $classes),
            'data'                 => $dataAttributes,
            'href'                 => $this->arguments['href'],
            'iconIdentifier'       => $this->arguments['iconIdentifier'],
            'label'                => $this->arguments['label'],
            'showSpinner'          => !empty($this->arguments['spinnerText']) || !empty($this->arguments['spinnerTextHidden']),
            'tag'                  => $this->arguments['tag'],
            'type'                 => $this->arguments['type'],
        ]);

        return $view->render();
    }

    /**
     * @param array<string, mixed> $additionalAttributes
     */
    private function processAdditionalAttributes(array &$additionalAttributes): void
    {
        if ($this->arguments['href']) {
            $this->arguments['tag'] = 'a';

            if ($this->arguments['externalLink']) {
                $this->arguments['rel']    = 'noopener noreferrer';
                $this->arguments['target'] = '_blank';
            }
        }

        $tag = $this->arguments['tag'];

        if ('a' === $tag) {
            foreach ([
                         'href',
                         'rel',
                         'target',
                     ] as $attributeName) {
                if (!empty($this->arguments[$attributeName])) {
                    $additionalAttributes[$attributeName] = $this->arguments[$attributeName];
                }
            }
        } elseif ('button' === $tag) {
            $additionalAttributes['type'] = $this->arguments['type'];
        }
    }

    /**
     * @param string[] $classes
     */
    private function processClasses(array &$classes): void
    {
        $classes[] = 'align-items-center';
        $classes[] = 'btn';
        $classes[] = 'd-flex';

        $classes = array_unique($classes);
        sort($classes);
    }

    /**
     * @param array<string, mixed> $dataAttributes
     */
    private function processDataAttributes(array &$dataAttributes): void
    {
        if (!empty($this->arguments['spinnerText'])) {
            $dataAttributes['psbits-spinner-phase-out'] = '';
            $dataAttributes['psbits-spinner-text']      = $this->arguments['spinnerText'];
        } elseif (!empty($this->arguments['spinnerTextHidden'])) {
            $dataAttributes['psbits-spinner-text-hidden'] = $this->arguments['spinnerTextHidden'];

            if (!empty($this->arguments['iconIdentifier'])) {
                $dataAttributes['psbits-spinner-phase-out'] = '';
            }
        }
    }
}
