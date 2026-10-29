<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\ViewHelpers\GlobalVariables;

use PSBits\Foundation\ViewHelpers\GlobalVariables\AbstractGlobalVariablesViewHelper;
use PSBits\FrontendTemplates\Service\GlobalVariableProviders\CurrentPageProvider;

/**
 * Class CurrentPageViewHelper
 *
 * @package PSBits\FrontendTemplates\ViewHelpers\GlobalVariables
 */
class CurrentPageViewHelper extends AbstractGlobalVariablesViewHelper
{
    protected function getBaseKey(): string
    {
        return CurrentPageProvider::class;
    }
}
