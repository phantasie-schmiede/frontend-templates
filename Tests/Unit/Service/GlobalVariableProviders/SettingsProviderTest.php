<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\Service\GlobalVariableProviders;

use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Domain\Repository\Settings\HeaderButtonRepository;
use PSBits\FrontendTemplates\Service\GlobalVariableProviders\SettingSourceInterface;
use PSBits\FrontendTemplates\Service\GlobalVariableProviders\SettingsProvider;
use PSBits\FrontendTemplates\Tests\Unit\Fixtures\SettingSourceFixture;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionProperty;
use RuntimeException;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class SettingsProviderTest
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\Service\GlobalVariableProviders
 */
class SettingsProviderTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The registered sources are static state, so every test starts clean.
        $this->resetSources();
        SettingSourceFixture::$results   = [];
        SettingSourceFixture::$queriedPids = [];
    }

    protected function tearDown(): void
    {
        $this->resetSources();
        unset($GLOBALS['TYPO3_REQUEST']);

        parent::tearDown();
    }

    #[Test]
    public function aSourceWithoutTheInterfaceIsRejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1773992928);

        SettingsProvider::registerSource(stdClass::class);
    }

    #[Test]
    public function aDuplicateSourceIsRejected(): void
    {
        SettingsProvider::registerSource(SettingSourceFixture::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1773992985);

        SettingsProvider::registerSource(SettingSourceFixture::class, 'buttons');
    }

    #[Test]
    public function reRegisteringTheSameSourceIsIgnored(): void
    {
        SettingsProvider::registerSource(SettingSourceFixture::class, 'fixtures');
        SettingsProvider::registerSource(SettingSourceFixture::class, 'fixtures');

        self::assertSame(['fixtures' => SettingSourceFixture::class], $this->getSources());
    }

    #[Test]
    public function aDuplicateIdentifierIsRejected(): void
    {
        SettingsProvider::registerSource(SettingSourceFixture::class, 'buttons');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1773993176);

        SettingsProvider::registerSource(HeaderButtonRepository::class, 'buttons');
    }

    #[Test]
    public function sourcesAreRegisteredUnderTheirClassOrIdentifier(): void
    {
        SettingsProvider::registerSource(SettingSourceFixture::class, 'fixtures');
        SettingsProvider::registerSource(HeaderButtonRepository::class);

        self::assertSame(SettingSourceFixture::class, $this->getSources()['fixtures']);
        self::assertSame(HeaderButtonRepository::class, $this->getSources()[HeaderButtonRepository::class]);
    }

    #[Test]
    public function theSettingsOfTheRootlinePagesAreCollected(): void
    {
        SettingsProvider::registerSource(SettingSourceFixture::class, 'fixtures');
        SettingSourceFixture::$results = [
            2 => ['label' => 'Button'],
        ];

        $this->provideRequest([
            ['uid' => 1, 'pid' => 0],
            ['uid' => 2, 'pid' => 1],
        ]);

        $settings = (new SettingsProvider())->getGlobalVariables();

        self::assertSame(['fixtures' => ['label' => 'Button']], $settings);
        self::assertSame([2], SettingSourceFixture::$queriedPids);
    }

    #[Test]
    public function theRootlineIsTraversedUntilTheSettingsAreFound(): void
    {
        SettingsProvider::registerSource(SettingSourceFixture::class, 'fixtures');
        SettingSourceFixture::$results = [
            1 => ['label' => 'Button'],
        ];

        $this->provideRequest([
            ['uid' => 1, 'pid' => 0],
            ['uid' => 2, 'pid' => 1],
        ]);

        $settings = (new SettingsProvider())->getGlobalVariables();

        self::assertSame(['fixtures' => ['label' => 'Button']], $settings);
        self::assertSame([2, 1], SettingSourceFixture::$queriedPids);
    }

    #[Test]
    public function withoutSettingsAnEmptyArrayIsReturned(): void
    {
        SettingsProvider::registerSource(SettingSourceFixture::class, 'fixtures');

        $this->provideRequest([
            ['uid' => 1, 'pid' => 0],
            ['uid' => 2, 'pid' => 1],
        ]);

        self::assertSame([], (new SettingsProvider())->getGlobalVariables());
        self::assertSame([2, 1], SettingSourceFixture::$queriedPids);
    }

    /**
     * @param array<int, array<string, mixed>> $localRootLine
     */
    private function provideRequest(array $localRootLine): void
    {
        $pageInformation = new PageInformation();
        $pageInformation->setLocalRootLine($localRootLine);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttribute')
            ->with('frontend.page.information')
            ->willReturn($pageInformation);
        $GLOBALS['TYPO3_REQUEST'] = $request;
    }

    private function resetSources(): void
    {
        (new ReflectionProperty(SettingsProvider::class, 'sources'))
            ->setValue(null, []);
    }

    /**
     * @return array<string, class-string<SettingSourceInterface>>
     */
    private function getSources(): array
    {
        return (new ReflectionProperty(SettingsProvider::class, 'sources'))
            ->getValue(null);
    }
}
