<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Utility;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\Model\RecordStateFactory;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use function in_array;

/**
 * Class SlugUtility
 *
 * @package PSBits\Templates\Utility
 */
class SlugUtility
{
    /**
     * @source: https://stackoverflow.com/questions/63304723/typo3-extbase-how-to-create-unique-slug-within-create-action
     * @throws Exception
     * @throws SiteNotFoundException
     */
    public static function generateUniqueSlug(int $uid, string $tableName, string $slugFieldName = 'slug'): false|string
    {
        /** @var Connection $connection */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($tableName);

        $record = $queryBuilder->select('*')
            ->from($tableName)
            ->where(
                $queryBuilder->expr()
                    ->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();
        if (!$record) {
            return false;
        }

        // Get field configuration
        $fieldConfig = $GLOBALS['TCA'][$tableName]['columns'][$slugFieldName]['config'];
        $evalInfo = GeneralUtility::trimExplode(',', $fieldConfig['eval'], true);

        // Initialize Slug helper
        /** @var SlugHelper $slugHelper */
        $slugHelper = GeneralUtility::makeInstance(
            SlugHelper::class,
            $tableName,
            $slugFieldName,
            $fieldConfig
        );

        // Generate slug
        $slug = $slugHelper->generate($record, $record['pid']);
        $state = RecordStateFactory::forName($tableName)
            ->fromArray($record, $record['pid'], $record['uid']);

        // Build slug depending on eval configuration
        if (in_array('uniqueInSite', $evalInfo, true)) {
            $slug = $slugHelper->buildSlugForUniqueInSite($slug, $state);
        } elseif (in_array('uniqueInPid', $evalInfo, true)) {
            $slug = $slugHelper->buildSlugForUniqueInPid($slug, $state);
        } elseif (in_array('unique', $evalInfo, true)) {
            $slug = $slugHelper->buildSlugForUniqueInTable($slug, $state);
        }

        return $slug;
    }
}
