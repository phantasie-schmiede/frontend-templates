<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\ViewHelpers\Format;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ReduceWhitespaceViewHelper
 *
 * @package PSBits\FrontendTemplates\ViewHelpers\Format
 */
class ReduceWhitespaceViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'replaceLinebreaks',
            'bool',
            'Convert all line breaks to spaces before normalizing whitespace.',
            false,
            false
        );
        $this->registerArgument(
            'value',
            'string',
            'String to normalize. Uses child content when omitted.',
            false
        );
    }

    public function render(): string
    {
        $value = $this->arguments['value'] ?? $this->renderChildren();
        $value = (string)$value;

        if ($this->arguments['replaceLinebreaks']) {
            $value = preg_replace('/\r\n|\r|\n/', ' ', $value);
            $value = preg_replace('/[^\S\r\n]{2,}/', ' ', $value);

            return trim($value);
        }

        // Normalize line endings first, then collapse spaces and repeated line breaks.
        $value = preg_replace('/\r\n|\r/', "\n", $value);
        $value = preg_replace('/[^\S\r\n]{2,}/', ' ', $value);
        $value = preg_replace('/[ \t]*\n[ \t]*/', "\n", $value);
        $value = preg_replace('/\n{2,}/', "\n", $value);

        return trim($value);
    }
}
