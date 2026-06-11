<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\ViewHelpers\Component;

use JsonException;
use PSBits\Foundation\Utility\ContextUtility;
use PSBits\Templates\Data\ExtensionInformation;
use PSBits\Templates\Service\ViewService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

use function is_string;

/**
 * Class ButtonViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\Component
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
            'sets tag to "a", target to _blank and rel to "noopener noreferrer"',
            false,
            false
        );
        $this->registerArgument('href', 'string', 'href attribute if tag is set to "a"');
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
        $additionalAttributes = $this->arguments['additionalAttributes'];
        $dataAttributes = $this->arguments['data'];
        $classes = $this->arguments['additionalClasses'];

        $this->processAdditionalAttributes($additionalAttributes);
        $this->processClasses($classes);
        $this->processDataAttributes($dataAttributes);

        $view = $this->viewService->createStandaloneView(
            $this->extensionInformation,
            ContextUtility::getRequest(),
            'ViewHelpers/Component/Button'
        );
        $view->assignMultiple([
            'additionalAttributes' => $additionalAttributes ?? [],
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

    private function processAdditionalAttributes(array &$additionalAttributes): void
    {
        if ($this->arguments['externalLink']) {
            $this->arguments['rel'] = 'noopener noreferrer';
            $this->arguments['tag'] = 'a';
            $this->arguments['target'] = '_blank';
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

    private function processClasses(array|string &$classes): void
    {
        if (is_string($classes)) {
            $classes = GeneralUtility::trimExplode(' ', $classes, true);
        }

        $classes[] = 'btn';

        if (!empty($this->arguments['iconIdentifier'])) {
            $classes[] = 'align-items-center';
            $classes[] = 'd-flex';
        }

        $classes = array_unique($classes);
        sort($classes);
    }

    private function processDataAttributes(array &$dataAttributes): void
    {
        if (!empty($this->arguments['spinnerText'])) {
            $dataAttributes['psbits-spinner-phase-out'] = '';
            $dataAttributes['psbits-spinner-text'] = $this->arguments['spinnerText'];
        } elseif (!empty($this->arguments['spinnerTextHidden'])) {
            $dataAttributes['psbits-spinner-text-hidden'] = $this->arguments['spinnerTextHidden'];

            if (!empty($this->arguments['iconIdentifier'])) {
                $dataAttributes['psbits-spinner-phase-out'] = '';
            }
        }
    }
}
