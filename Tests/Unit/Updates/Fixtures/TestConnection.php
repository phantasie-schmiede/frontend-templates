<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\Updates\Fixtures;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Connection;

use function implode;

class TestConnection extends Connection
{
    public function select(array $columns, string $tableName, array $identifiers = [], array $groupBy = [], array $orderBy = [], int $limit = 0, int $offset = 0): Result
    {
        $query = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $this->quoteIdentifier($tableName);

        if ([] !== $identifiers) {
            $query .= ' WHERE uid = ' . (int)$identifiers['uid'];
        }

        return $this->executeQuery($query);
    }

    /**
     * Skip TYPO3's cached schema lookup in isolated SQLite tests.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $types
     */
    protected function ensureDatabaseValueTypes(string $tableName, array &$data, array &$types): void
    {
    }
}
