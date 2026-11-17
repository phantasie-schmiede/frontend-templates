<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Updates;

use Doctrine\DBAL\Exception;
use Generator;
use RuntimeException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

use function array_map;
use function implode;
use function sprintf;

final readonly class ExtensionFieldPrefixUpgradeWizard implements UpgradeWizardInterface
{
    private const array FIELDS = [
        'pages'             => ['bs_container_type', 'row_highlight_1', 'row_highlight_2', 'row_highlight_3'],
        'sys_file_metadata' => ['ai_status', 'copyright'],
        'tt_content'        => [
            'accordion_items', 'accordion_style', 'autoplay', 'background_variant', 'bootstrap_type',
            'button_size', 'cta_label', 'eager_loading', 'emphasis_style', 'enable_filter', 'error_message',
            'faq_items', 'full_width', 'grayscale', 'introtext', 'layout_variant', 'links', 'loop_slides',
            'show_navigation', 'show_overlay', 'show_pagination', 'success_message', 'teasers', 'text_align',
        ],
    ];
    private const string NEW_PREFIX = 'tx_frontendtemplates_';
    private const string OLD_PREFIX = 'tx_templates_';

    public function __construct(private ConnectionPool $connectionPool)
    {
    }

    /**
     * @throws Exception
     * @throws RuntimeException
     */
    public function executeUpdate(): bool
    {
        /* Validate every destination before writing anything, including fields on other connections. */
        foreach ($this->getUpdates() as $update) {
            if (!$update['writable']) {
                throw new RuntimeException(sprintf(
                    'Cannot migrate %s, uid %d, field %s: the destination is missing or contains a conflicting value. Resolve the conflict and run the wizard again. Legacy data has not been deleted.',
                    $update['table'],
                    $update['uid'],
                    $update['field'],
                ));
            }
        }

        foreach ($this->getUpdates() as $update) {
            $this->connectionPool->getConnectionForTable($update['table'])->update(
                $update['table'],
                [$update['field'] => $update['value']],
                ['uid' => $update['uid']],
            );
        }

        return true;
    }

    public function getDescription(): string
    {
        return 'Migrate legacy tx_templates_* values in pages, file metadata and Content Blocks to tx_frontendtemplates_*, including the Feature link relation. Keep the old columns until this wizard has completed. Existing non-default destination values are never overwritten.';
    }

    /**
     * @return list<class-string>
     */
    public function getPrerequisites(): array
    {
        return [DatabaseUpdatedPrerequisite::class];
    }

    public function getTitle(): string
    {
        return 'PSBits Frontend Templates: migrate extension field prefixes';
    }

    /**
     * @throws Exception
     */
    public function updateNecessary(): bool
    {
        foreach ($this->getUpdates() as $update) {
            return true;
        }

        return false;
    }

    /**
     * @return Generator<array{field: string, table: string, uid: int, value: mixed, writable: bool}>
     * @throws Exception
     */
    private function getUpdates(): Generator
    {
        foreach (self::FIELDS as $table => $suffixes) {
            $connection = $this->connectionPool->getConnectionForTable($table);
            $schema     = $connection->createSchemaManager();

            if (!$schema->tablesExist([$table])) {
                continue;
            }

            $columns = $schema->listTableColumns($table);
            $fields  = [];

            foreach ($suffixes as $suffix) {
                $old = self::OLD_PREFIX . $suffix;
                $new = self::NEW_PREFIX . $suffix;

                if (isset($columns[$old])) {
                    $fields[$old] = $new;
                }
            }

            $select = ['uid'];

            foreach ($fields as $old => $new) {
                $select[] = $old;

                if (isset($columns[$new])) {
                    $select[] = $new;
                }
            }

            $hasLinkRelation = 'tt_content' === $table && isset($columns['fieldname']);

            if ($hasLinkRelation) {
                $select[] = 'fieldname';
            }

            if ([] === $fields && !$hasLinkRelation) {
                continue;
            }

            /* Direct connection queries also include hidden, deleted, translated and workspace records. */
            $result = $connection->executeQuery('SELECT '
                . implode(', ', array_map($connection->quoteIdentifier(...), $select))
                . ' FROM ' . $connection->quoteIdentifier($table));

            while ($row = $result->fetchAssociative()) {
                foreach ($fields as $old => $new) {
                    $value = $row[$old];

                    if (null === $value || (string)$columns[$old]->getDefault() === (string)$value
                        || (isset($columns[$new]) && (string)$row[$new] === (string)$value)) {
                        continue;
                    }

                    yield [
                        'field'    => $new,
                        'table'    => $table,
                        'uid'      => (int)$row['uid'],
                        'value'    => $value,
                        'writable' => isset($columns[$new]) && (null === $row[$new]
                            || (string)$columns[$new]->getDefault() === (string)$row[$new]),
                    ];
                }

                if ($hasLinkRelation && self::OLD_PREFIX . 'links' === $row['fieldname']) {
                    yield [
                        'field'    => 'fieldname',
                        'table'    => $table,
                        'uid'      => (int)$row['uid'],
                        'value'    => self::NEW_PREFIX . 'links',
                        'writable' => true,
                    ];
                }
            }
        }
    }
}
