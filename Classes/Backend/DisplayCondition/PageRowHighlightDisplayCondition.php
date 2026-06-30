<?php

declare(strict_types=1);

namespace PSBits\Templates\Backend\DisplayCondition;

use TYPO3\CMS\Backend\Utility\BackendUtility;

use function in_array;
use function is_array;

class PageRowHighlightDisplayCondition
{
    private const LAYOUT_ROW_MAP = [
        'pagets__blank'                                   => 1,
        'pagets__grid-2x2'                                => 1,
        'pagets__oneColumn'                               => 1,
        'pagets__oneColumnThreeColumns-1-1-1'             => 2,
        'pagets__oneColumnThreeColumns-1-1-1-oneColumn'   => 3,
        'pagets__oneColumnThreeColumns-1-2-1-oneColumn'   => 3,
        'pagets__oneColumnTwoColumns-1-1'                 => 2,
        'pagets__oneColumnTwoColumns-1-2-oneColumn'       => 3,
        'pagets__oneColumnTwoColumns-2-1-oneColumn'       => 3,
        'pagets__threeColumns-1-1-1'                      => 1,
        'pagets__threeColumns-1-2-1'                      => 1,
        'pagets__twoColumns-1-1'                          => 1,
        'pagets__twoColumns-1-2'                          => 1,
        'pagets__twoColumns-1-3'                          => 1,
        'pagets__twoColumns-2-1'                          => 1,
        'pagets__twoColumns-3-1'                          => 1,
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

        $availableRows = self::LAYOUT_ROW_MAP[$layoutIdentifier] ?? 1;

        return $row <= $availableRows;
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

        $layoutIdentifier = $this->normalizeLayoutIdentifier((string)($record['backend_layout'] ?? ''));
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

    private function normalizeLayoutIdentifier(string $layoutIdentifier): string
    {
        if (in_array($layoutIdentifier, ['', '-1', '0'], true)) {
            return '';
        }

        return $layoutIdentifier;
    }
}
