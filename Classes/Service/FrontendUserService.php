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

namespace PSB\PsbTemplates\Service;

use PSB\PsbTemplates\Domain\Model\FrontendUser;
use PSB\PsbTemplates\Traits\PropertyInjection\FrontendUserRepositoryTrait;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Context\Exception\AspectPropertyNotFoundException;

/**
 * Class FrontendUserService
 *
 * @package PSB\PsbTemplates\Service
 */
class FrontendUserService extends \PSB\PsbFoundation\Service\FrontendUserService
{
    use FrontendUserRepositoryTrait;

    /**
     * @return FrontendUser|null
     * @throws AspectNotFoundException
     * @throws AspectPropertyNotFoundException
     */
    public function getCurrentUser(): ?FrontendUser
    {
        return $this->overriddenFrontendUserRepository->findByUid($this->getCurrentUserId());
    }
}
