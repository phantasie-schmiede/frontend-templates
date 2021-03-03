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

namespace PSB\PsbTemplates\Traits\PropertyInjection;

use PSB\PsbTemplates\Domain\Repository\FrontendUserRepository;

/**
 * Trait FrontendUserRepositoryTrait
 *
 * @package PSB\PsbTemplates\Traits\Properties
 */
trait FrontendUserRepositoryTrait
{
    /**
     * @var FrontendUserRepository|null
     */
    protected ?FrontendUserRepository $overriddenFrontendUserRepository = null;

    /**
     * @param FrontendUserRepository $frontendUserRepository
     */
    public function injectOverriddenFrontendUserRepository(FrontendUserRepository $frontendUserRepository): void
    {
        $this->overriddenFrontendUserRepository = $frontendUserRepository;
    }
}
