<?php
declare(strict_types=1);

/*
 * This file is part of PSB Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSB\PsbTemplates\ViewHelpers;

use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconProvider\AbstractSvgIconProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class SvgIconViewHelper
 *
 * @package PSB\PsbTemplates\ViewHelpers
 */
class SvgIconViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function __construct(
        protected readonly IconFactory $iconFactory,
    ) {
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'height',
            'int',
            'custom height of the icon'
        );
        $this->registerArgument(
            'identifier',
            'string',
            'identifier of the icon in the icon registry',
            true
        );
        $this->registerArgument(
            'viewBox',
            'string',
            'custom viewBox of the icon'
        );
        $this->registerArgument(
            'width',
            'int',
            'custom width of the icon'
        );
    }

    public function render(): string
    {
        $icon = $this->iconFactory->getIcon($this->arguments['identifier']);
        $inlineSvg = $icon->getMarkup(AbstractSvgIconProvider::MARKUP_IDENTIFIER_INLINE);
        $replacements = [];

        foreach ([
                     'height',
                     'viewBox',
                     'width',
                 ] as $attributeName) {
            if (null !== $this->arguments[$attributeName]) {
                $replacements[] = [
                    'pattern'     => '/' . $attributeName . '="[^"]+"/',
                    'replacement' => $attributeName . '="' . $this->arguments[$attributeName] . '"',
                ];
            }
        }

        return preg_replace(
            array_column($replacements, 'pattern'),
            array_column($replacements, 'replacement'),
            $inlineSvg
        );
    }
}
