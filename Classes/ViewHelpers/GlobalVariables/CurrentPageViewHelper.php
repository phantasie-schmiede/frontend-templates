<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\ViewHelpers\GlobalVariables;

use PSBits\Foundation\ViewHelpers\GlobalVariables\AbstractGlobalVariablesViewHelper;
use PSBits\Templates\Service\GlobalVariableProviders\CurrentPageProvider;

/**
 * Class CurrentPageViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\GlobalVariables
 */
class CurrentPageViewHelper extends AbstractGlobalVariablesViewHelper
{
    protected function getBaseKey(): string
    {
        return CurrentPageProvider::class;
    }
}
