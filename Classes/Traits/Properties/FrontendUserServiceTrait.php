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

namespace PSB\PsbTemplates\Traits\Properties;

use PSB\PsbTemplates\Service\FrontendUserService;

/**
 * Trait FrontendUserServiceTrait
 *
 * @package PSB\PsbTemplates\Traits\Properties
 */
trait FrontendUserServiceTrait
{
    /**
     * @var FrontendUserService|null
     */
    protected ?FrontendUserService $frontendUserService = null;

    /**
     * @param FrontendUserService $frontendUserService
     */
    public function injectFrontendUserService(FrontendUserService $frontendUserService): void
    {
        $this->frontendUserService = $frontendUserService;
    }
}
