<?php
declare(strict_types=1);
namespace PSB\PsbTemplates\Service\GlobalVariableProviders;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Daniel Ablass <dn@phantasie-schmiede.de>, PSbits
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use PSB\PsbFoundation\Service\GlobalVariableProviders\GlobalVariableProviderInterface;
use PSB\PsbFoundation\Traits\InjectionTrait;
use PSB\PsbTemplates\Utility\FrontendUserUtility;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Context\Exception\AspectPropertyNotFoundException;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class FrontendUserProvider
 *
 * @package PSB\PsbTemplates\Service\GlobalVariableProviders
 */
class FrontendUserProvider implements GlobalVariableProviderInterface
{
    use InjectionTrait;

    /**
     * @var bool
     */
    protected bool $cacheable = false;

    /**
     * @return array
     * @throws Exception
     * @throws AspectNotFoundException
     * @throws AspectPropertyNotFoundException
     */
    public function getGlobalVariables(): array
    {
        // not available in backend
        if ('BE' === TYPO3_MODE) {
            $this->setCacheable(true);

            return [];
        }

        $frontendUser = FrontendUserUtility::getCurrentUser();
        $this->setCacheable(true);

        return ['frontendUser' => $frontendUser];
    }

    /**
     * This must return false on first call. Otherwise the function getGlobalVariables() will never be called. When
     * returned data isn't supposed to change anymore, set function's return value to true.
     *
     * @return bool
     */
    public function isCacheable(): bool
    {
        return $this->cacheable;
    }

    /**
     * @param bool $cacheable
     */
    public function setCacheable(bool $cacheable): void
    {
        $this->cacheable = $cacheable;
    }
}
