<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Domain\Repository\Settings;

use PSBits\FrontendTemplates\Domain\Model\Settings\HeaderButton;
use PSBits\FrontendTemplates\Service\GlobalVariableProviders\SettingSourceInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class HeaderButtonRepository
 *
 * @extends Repository<HeaderButton>
 * @package PSBits\FrontendTemplates\Domain\Repository\Settings
 */
class HeaderButtonRepository extends Repository implements SettingSourceInterface
{
    /**
     * @return HeaderButton[]
     */
    public function findByPid(int $pid): array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
            ->setRespectStoragePage(false);

        return $query->matching(
            $query->equals('pid', $pid)
        )
            ->execute()
            ->toArray();
    }
}
