<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Functional\Updates;

use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Updates\ExtensionFieldPrefixUpgradeWizard;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ExtensionFieldPrefixUpgradeWizardTest extends FunctionalTestCase
{
    #[Test]
    public function modelAndContentBlockValuesAndCollectionRelationsAreMigrated(): void
    {
        $pool   = GeneralUtility::makeInstance(ConnectionPool::class);
        $fields = [
            'pages'             => ['bs_container_type', 'row_highlight_1', 'row_highlight_2', 'row_highlight_3'],
            'sys_file_metadata' => ['ai_status', 'copyright'],
            'tt_content'        => [
                'accordion_items', 'accordion_style', 'autoplay', 'background_variant', 'bootstrap_type',
                'button_size', 'cta_label', 'eager_loading', 'emphasis_style', 'enable_filter', 'error_message',
                'faq_items', 'full_width', 'grayscale', 'introtext', 'layout_variant', 'links', 'loop_slides',
                'show_navigation', 'show_overlay', 'show_pagination', 'success_message', 'teasers', 'text_align',
            ],
        ];

        foreach ($fields as $table => $suffixes) {
            $connection = $pool->getConnectionForTable($table);
            $schema     = $connection->createSchemaManager();
            $before     = $schema->introspectTable($table);
            $after      = clone $before;

            foreach ($suffixes as $suffix) {
                foreach (['tx_templates_', 'tx_frontendtemplates_'] as $prefix) {
                    $after->addColumn($prefix . $suffix, 'string', ['default' => '', 'length' => 255]);
                }
            }

            if ('tt_content' === $table) {
                $after->addColumn('fieldname', 'string', ['default' => '', 'length' => 255]);
            }

            $schema->alterTable($schema->createComparator()->compareTables($before, $after));
            $record = ['uid' => 1];

            foreach ($suffixes as $suffix) {
                $record['tx_templates_' . $suffix] = 'legacy-' . $suffix;
            }

            $connection->insert($table, $record);
        }

        $contentConnection = $pool->getConnectionForTable('tt_content');
        $contentConnection->insert('tt_content', [
            'deleted'    => 1,
            'fieldname'  => 'tx_templates_links',
            'hidden'     => 1,
            't3ver_wsid' => 1,
            'uid'        => 2,
        ]);
        $contentConnection->insert('tt_content', ['fieldname' => 'another_extension_links', 'uid' => 3]);
        $wizard = new ExtensionFieldPrefixUpgradeWizard($pool);

        self::assertTrue($wizard->updateNecessary());
        self::assertTrue($wizard->executeUpdate());
        self::assertFalse($wizard->updateNecessary());
        self::assertTrue($wizard->executeUpdate());

        foreach ($fields as $table => $suffixes) {
            $row = $pool->getConnectionForTable($table)->select(['*'], $table, ['uid' => 1])->fetchAssociative();

            foreach ($suffixes as $suffix) {
                self::assertSame('legacy-' . $suffix, $row['tx_frontendtemplates_' . $suffix]);
                self::assertSame('legacy-' . $suffix, $row['tx_templates_' . $suffix]);
            }
        }

        self::assertSame('tx_frontendtemplates_links', $contentConnection->executeQuery('SELECT fieldname FROM tt_content WHERE uid = 2')->fetchOne());
        self::assertSame('another_extension_links', $contentConnection->select(['fieldname'], 'tt_content', ['uid' => 3])->fetchOne());
    }
}
