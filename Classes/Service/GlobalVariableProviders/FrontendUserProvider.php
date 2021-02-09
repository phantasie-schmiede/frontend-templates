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

namespace PSB\PsbTemplates\Service\GlobalVariableProviders;

use PSB\PsbFoundation\Service\GlobalVariableProviders\GlobalVariableProviderInterface;
use PSB\PsbTemplates\Service\FrontendUserService;
use TYPO3\CMS\Core\Context\Exception\AspectPropertyNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class FrontendUserProvider
 *
 * @package PSB\PsbTemplates\Service\GlobalVariableProviders
 */
class FrontendUserProvider implements GlobalVariableProviderInterface
{
    /**
     * @var bool
     */
    protected bool $cacheable = false;

    /**
     * @return bool
     */
    public static function isAvailableDuringBootProcess(): bool
    {
        return false;
    }

    /**
     * @return array
     * @throws AspectPropertyNotFoundException
     * @throws Exception
     */
    public function getGlobalVariables(): array
    {
        // not available in backend
        if ('BE' === TYPO3_MODE) {
            $this->setCacheable(true);

            return [];
        }

        $frontendUserService = GeneralUtility::makeInstance(FrontendUserService::class);
        $frontendUser = $frontendUserService->getCurrentUser();
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
