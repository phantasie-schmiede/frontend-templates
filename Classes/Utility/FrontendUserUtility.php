<?php
declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace PSB\PsbTemplates\Utility;

use PSB\PsbFoundation\Utility\ObjectUtility;
use PSB\PsbTemplates\Domain\Model\FrontendUser;
use PSB\PsbTemplates\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Context\Exception\AspectPropertyNotFoundException;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class FrontendUserUtility
 *
 * @package PSB\PsbTemplates\Utility
 */
class FrontendUserUtility extends \PSB\PsbFoundation\Utility\FrontendUserUtility
{
    /**
     * @return FrontendUser|null
     * @throws AspectNotFoundException
     * @throws AspectPropertyNotFoundException
     * @throws Exception
     */
    public static function getCurrentUser(): ?FrontendUser
    {
        return ObjectUtility::get(FrontendUserRepository::class)
            ->findByUid(self::getCurrentUserId());
    }
}
