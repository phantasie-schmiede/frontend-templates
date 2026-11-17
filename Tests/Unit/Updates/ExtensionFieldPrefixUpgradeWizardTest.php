<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\Updates;

use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PSBits\FrontendTemplates\Tests\Unit\Updates\Fixtures\TestConnection;
use PSBits\FrontendTemplates\Updates\ExtensionFieldPrefixUpgradeWizard;
use RuntimeException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionFieldPrefixUpgradeWizardTest extends TestCase
{
    private Connection                        $connection;
    private ExtensionFieldPrefixUpgradeWizard $wizard;

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection([
            'driver'       => 'pdo_sqlite',
            'memory'       => true,
            'wrapperClass' => TestConnection::class,
        ]);
        $pool = $this->createMock(ConnectionPool::class);
        $pool->method('getConnectionForTable')->willReturn($this->connection);
        $this->wizard = new ExtensionFieldPrefixUpgradeWizard($pool);
    }

    protected function tearDown(): void
    {
        $this->connection->close();
        GeneralUtility::purgeInstances();
    }

    #[Test]
    public function conflictingDestinationsPreventAllWrites(): void
    {
        $this->createPagesTable();
        $this->connection->insert('pages', ['tx_templates_bs_container_type' => 'fluid', 'uid' => 1]);
        $this->connection->insert('pages', [
            'tx_frontendtemplates_bs_container_type' => 'lg',
            'tx_templates_bs_container_type'         => 'sm',
            'uid'                                    => 2,
        ]);

        try {
            $this->wizard->executeUpdate();
            self::fail('A conflicting destination must prevent the migration.');
        } catch (RuntimeException $exception) {
            self::assertStringContainsString('uid 2', $exception->getMessage());
        }

        self::assertSame('', $this->connection->select(['tx_frontendtemplates_bs_container_type'], 'pages', ['uid' => 1])->fetchOne());
        self::assertSame('lg', $this->connection->select(['tx_frontendtemplates_bs_container_type'], 'pages', ['uid' => 2])->fetchOne());
    }

    #[Test]
    public function defaultLegacyValuesDoNotOverwriteEditedDestinations(): void
    {
        $this->createPagesTable();
        $this->connection->insert('pages', ['tx_frontendtemplates_bs_container_type' => 'lg', 'uid' => 1]);

        self::assertFalse($this->wizard->updateNecessary());
        self::assertTrue($this->wizard->executeUpdate());
        self::assertSame('lg', $this->connection->select(['tx_frontendtemplates_bs_container_type'], 'pages', ['uid' => 1])->fetchOne());
    }

    #[Test]
    public function missingDestinationsDoNotLoseLegacyData(): void
    {
        $this->connection->executeStatement("CREATE TABLE pages (uid INTEGER PRIMARY KEY, tx_templates_bs_container_type VARCHAR(20) DEFAULT '')");
        $this->connection->insert('pages', ['tx_templates_bs_container_type' => 'fluid', 'uid' => 1]);

        self::assertTrue($this->wizard->updateNecessary());
        $this->expectException(RuntimeException::class);
        $this->wizard->executeUpdate();
    }

    #[Test]
    public function newInstallationsAndMissingLegacyColumnsNeedNoUpdate(): void
    {
        self::assertFalse($this->wizard->updateNecessary());
        $this->connection->executeStatement('CREATE TABLE pages (uid INTEGER PRIMARY KEY)');
        $this->connection->insert('pages', ['uid' => 1]);

        self::assertFalse($this->wizard->updateNecessary());
        self::assertTrue($this->wizard->executeUpdate());
    }

    #[Test]
    public function valuesAreMigratedWithoutDeletingLegacyDataAndRepeatedRunsAreSafe(): void
    {
        $this->createPagesTable();
        $this->connection->insert('pages', ['tx_templates_bs_container_type' => 'fluid', 'uid' => 1]);

        self::assertTrue($this->wizard->updateNecessary());
        self::assertTrue($this->wizard->executeUpdate());
        self::assertFalse($this->wizard->updateNecessary());
        self::assertTrue($this->wizard->executeUpdate());
        $row = $this->connection->select(['*'], 'pages', ['uid' => 1])->fetchAssociative();
        self::assertSame('fluid', $row['tx_frontendtemplates_bs_container_type']);
        self::assertSame('fluid', $row['tx_templates_bs_container_type']);
    }

    private function createPagesTable(): void
    {
        $this->connection->executeStatement("CREATE TABLE pages (uid INTEGER PRIMARY KEY, tx_templates_bs_container_type VARCHAR(20) DEFAULT '', tx_frontendtemplates_bs_container_type VARCHAR(20) DEFAULT '')");
    }
}
