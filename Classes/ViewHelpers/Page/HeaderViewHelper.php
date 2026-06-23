<?php

declare(strict_types=1);

namespace PSBits\Templates\ViewHelpers\Page;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

use function in_array;

/**
 * Class HeaderViewHelper
 *
 * Adds HTML to the TYPO3 page header once per unique identifier.
 *
 * Usage in Fluid:
 * <templates:page.header
 *     html="<meta name='robots' content='noindex'>"
 *     identifier="my-header-id" />
 * or with child content:
 * <templates:page.header identifier="my-header-id">
 *     <meta
 *         content="noindex"
 *         name="robots" />
 * </templates:page.header>
 *
 * @package PSBits\Templates\ViewHelpers\Page
 */
class HeaderViewHelper extends AbstractViewHelper
{
    protected static array $headerDataIdentifiers = [];
    protected              $escapeOutput          = false;

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'identifier',
            'string',
            'An identifier for the header data, which is used to prevent duplicate entries',
            true
        );
        $this->registerArgument('html', 'string', 'The HTML content to be added to the header');
    }

    public function render(): string
    {
        if (in_array($this->arguments['identifier'], self::$headerDataIdentifiers, true)) {
            return '';
        }

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $html = $this->arguments['html'] ?? $this->renderChildren();
        $pageRenderer->addHeaderData($html);
        self::$headerDataIdentifiers[] = $this->arguments['identifier'];

        return '';
    }
}
