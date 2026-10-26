<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\ViewHelpers\Page;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RowHighlightClassViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\Page
 */
class RowHighlightClassViewHelper extends AbstractViewHelper
{
    private const array CLASS_MAP = [
        'emphasis' => 'page-row-highlight-emphasis',
        'subtle'   => 'page-row-highlight-subtle',
    ];
    private const array TOKEN_FIELDS = [
        1 => 'tx_templates_row_highlight_1',
        2 => 'tx_templates_row_highlight_2',
        3 => 'tx_templates_row_highlight_3',
    ];

    public function initializeArguments(): void
    {
        $this->registerArgument('currentPage', 'array', 'Current page record', false, []);
        $this->registerArgument('row', 'int', 'Row number', true);
    }

    public function render(): string
    {
        $row = (int)$this->arguments['row'];

        if (!isset(self::TOKEN_FIELDS[$row])) {
            return '';
        }

        /** @var array<string, mixed> $currentPage */
        $currentPage = $this->arguments['currentPage'];
        $tokenField  = self::TOKEN_FIELDS[$row];
        $token       = (string)($currentPage[$tokenField] ?? 'none');

        return self::CLASS_MAP[$token] ?? '';
    }
}
