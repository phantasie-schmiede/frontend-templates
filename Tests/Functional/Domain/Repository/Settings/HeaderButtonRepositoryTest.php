<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Functional\Domain\Repository\Settings;

use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Domain\Model\Settings\HeaderButton;
use PSBits\FrontendTemplates\Domain\Repository\Settings\HeaderButtonRepository;
use PSBits\FrontendTemplates\Enum\BootstrapType;
use ReflectionProperty;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Class HeaderButtonRepositoryTest
 *
 * @package PSBits\FrontendTemplates\Tests\Functional\Domain\Repository\Settings
 */
class HeaderButtonRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3/sysext/fluid_styled_content',
        'typo3conf/ext/friendsoftypo3/content-blocks',
        'typo3conf/ext/psbits/foundation',
        'typo3conf/ext/psbits/frontend-templates',
        'typo3conf/ext/psbits/view-helpers',
        'typo3conf/ext/wapplersystems/ws-scss',
    ];

    #[Test]
    public function headerButtonsAreFoundByTheirStoragePage(): void
    {
        $repository       = GeneralUtility::makeInstance(HeaderButtonRepository::class);
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

        $repository->add($this->createHeaderButton('GitHub', 'https://github.com', BootstrapType::Primary, 1));
        $repository->add($this->createHeaderButton('X', 'https://x.com', BootstrapType::Danger, 2));
        $persistenceManager->persistAll();
        $persistenceManager->clearState();

        $rootButtons = $repository->findByPid(1);

        self::assertCount(1, $rootButtons);
        self::assertSame('GitHub', $rootButtons[0]->getLabel());
        self::assertSame('https://github.com', $rootButtons[0]->getLink());
        self::assertSame(BootstrapType::Primary, $rootButtons[0]->getType());

        $childButtons = $repository->findByPid(2);

        self::assertCount(1, $childButtons);
        self::assertSame('X', $childButtons[0]->getLabel());

        self::assertCount(0, $repository->findByPid(3));
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
