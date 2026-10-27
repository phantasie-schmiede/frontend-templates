<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Functional\Service\GlobalVariableProviders;

use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Domain\Model\Settings\HeaderButton;
use PSBits\FrontendTemplates\Domain\Repository\Settings\HeaderButtonRepository;
use PSBits\FrontendTemplates\Enum\BootstrapType;
use PSBits\FrontendTemplates\Service\GlobalVariableProviders\SettingsProvider;
use PSBits\FrontendTemplates\Tests\Functional\Fixtures\FunctionalRequestFactory;
use ReflectionProperty;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Class SettingsProviderTest
 *
 * Runs the provider against the real source registered in ext_localconf.php: the header buttons of the pages in
 * the (local) rootline are collected from the database.
 *
 * @package PSBits\FrontendTemplates\Tests\Functional\Service\GlobalVariableProviders
 */
#[IgnoreDeprecations]
class SettingsProviderTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3/sysext/fluid_styled_content',
        'typo3conf/ext/friendsoftypo3/content-blocks',
        'typo3conf/ext/psbits/foundation',
        'typo3conf/ext/psbits/frontend-templates',
        'typo3conf/ext/psbits/view-helpers',
        'typo3conf/ext/wapplersystems/ws-scss',
    ];

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        parent::tearDown();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    #[Test]
    public function theRegisteredSourcesProvideTheSettingsOfTheRootline(): void
    {
        $repository       = GeneralUtility::makeInstance(HeaderButtonRepository::class);
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

        $repository->add($this->createHeaderButton('GitHub', 'https://github.com', BootstrapType::Primary, 1));
        $persistenceManager->persistAll();
        $persistenceManager->clearState();

        $pageInformation = new PageInformation();
        $pageInformation->setLocalRootLine([
            ['uid' => 1, 'pid' => 0],
            ['uid' => 2, 'pid' => 1],
        ]);

        $GLOBALS['TYPO3_REQUEST'] = FunctionalRequestFactory::createFrontendRequest([
            'frontend.page.information' => $pageInformation,
        ]);

        $settings = (new SettingsProvider())->getGlobalVariables();

        self::assertArrayHasKey('headerButtons', $settings);
        self::assertCount(1, $settings['headerButtons']);
        self::assertSame('GitHub', $settings['headerButtons'][0]->getLabel());
        self::assertSame('https://github.com', $settings['headerButtons'][0]->getLink());
    }

    private function createHeaderButton(string $label, string $link, BootstrapType $type, int $pid): HeaderButton
    {
        $button = GeneralUtility::makeInstance(HeaderButton::class);
        (new ReflectionProperty(HeaderButton::class, 'label'))
            ->setValue($button, $label);
        (new ReflectionProperty(HeaderButton::class, 'link'))
            ->setValue($button, $link);
        (new ReflectionProperty(HeaderButton::class, 'type'))
            ->setValue($button, $type);
        $button->setPid($pid);

        return $button;
    }
}
