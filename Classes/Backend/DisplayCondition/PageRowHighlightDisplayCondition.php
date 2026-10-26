<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Backend\DisplayCondition;

use TYPO3\CMS\Backend\Utility\BackendUtility;

use function in_array;
use function is_array;

/**
 * Class PageRowHighlightDisplayCondition
 *
 * @package PSBits\Templates\Backend\DisplayCondition
 */
class PageRowHighlightDisplayCondition
{
    private const array LAYOUT_ROW_MAP = [
        'blank'                                 => 1,
        'grid-2x2'                              => 1,
        'oneColumn'                             => 1,
        'oneColumnThreeColumns-1-1-1'           => 2,
        'oneColumnThreeColumns-1-1-1-oneColumn' => 3,
        'oneColumnThreeColumns-1-2-1-oneColumn' => 3,
        'oneColumnTwoColumns-1-1'               => 2,
        'oneColumnTwoColumns-1-2-oneColumn'     => 3,
        'oneColumnTwoColumns-2-1-oneColumn'     => 3,
        'threeColumns-1-1-1'                    => 1,
        'threeColumns-1-2-1'                    => 1,
        'twoColumns-1-1'                        => 1,
        'twoColumns-1-2'                        => 1,
        'twoColumns-1-3'                        => 1,
        'twoColumns-2-1'                        => 1,
        'twoColumns-3-1'                        => 1,
    ];

    /**
     * @param array<string, mixed> $parameters
     */
    public function matches(array $parameters): bool
    {
        $row = (int)($parameters['conditionParameters'][0] ?? 0);

        if (0 >= $row) {
            return false;
        }

        $layoutIdentifier = $this->resolveLayoutIdentifier($parameters);

        if ('' === $layoutIdentifier) {
            return 1 >= $row;
        }

        $availableRows = self::LAYOUT_ROW_MAP[str_replace('pagets__', '', $layoutIdentifier)] ?? 1;

        return $row <= $availableRows;
    }

    private function normalizeLayoutIdentifier(string $layoutIdentifier): string
    {
        if (in_array(
            $layoutIdentifier,
            [
                '',
                '-1',
                '0',
            ],
            true
        )) {
            return '';
        }

        return $layoutIdentifier;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function resolveLayoutIdentifier(array $parameters): string
    {
        $record = $parameters['record'] ?? [];

        if (!is_array($record)) {
            return '';
        }

        $layoutIdentifier = $this->normalizeLayoutIdentifier((string)($record['backend_layout'][0] ?? ''));

        if ('' !== $layoutIdentifier) {
            return $layoutIdentifier;
        }

        return $this->resolveLayoutIdentifierFromRootline($record);
    }

    /**
     * @param array<string, mixed> $record
     */
    private function resolveLayoutIdentifierFromRootline(array $record): string
    {
        $parentPageId = (int)($record['pid'] ?? 0);

        if (0 >= $parentPageId) {
            return '';
        }

        $currentPageId = $parentPageId;

        while (0 < $currentPageId) {
            /** @var array<string, mixed>|null $page */
            $page = BackendUtility::getRecord('pages', $currentPageId, 'backend_layout,backend_layout_next_level,pid');

            if (!is_array($page)) {
                break;
            }

            $layoutIdentifier = $this->normalizeLayoutIdentifier((string)($page['backend_layout_next_level'] ?? ''));

            if ('' !== $layoutIdentifier) {
                return $layoutIdentifier;
            }

            $layoutIdentifier = $this->normalizeLayoutIdentifier((string)($page['backend_layout'] ?? ''));

            if ('' !== $layoutIdentifier) {
                return $layoutIdentifier;
            }

            $currentPageId = (int)($page['pid'] ?? 0);
        }

        return '';
    }
}
