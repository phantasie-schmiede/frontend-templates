<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Functional\Utility;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Utility\SlugUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class SlugUtilityTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3/sysext/fluid_styled_content',
        'typo3conf/ext/friendsoftypo3/content-blocks',
        'typo3conf/ext/psbits/foundation',
        'typo3conf/ext/psbits/frontend-templates',
        'typo3conf/ext/psbits/view-helpers',
        'typo3conf/ext/wapplersystems/ws-scss',
    ];

    /**
     * @throws Exception
     */
    #[Test]
    public function aMissingPageYieldsFalse(): void
    {
        self::assertFalse(SlugUtility::generateUniqueSlug(999, 'pages'));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function aSlugIsGeneratedForAnExistingPage(): void
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('pages');
        $connection->insert('pages', [
            'pid'   => 0,
            'slug'  => '',
            'title' => 'Root Page',
            'uid'   => 1,
        ]);
        $connection->insert('pages', [
            'pid'   => 1,
            'slug'  => '',
            'title' => 'My Test Page',
            'uid'   => 2,
        ]);

        self::assertSame('/my-test-page', SlugUtility::generateUniqueSlug(2, 'pages'));
    }
}
