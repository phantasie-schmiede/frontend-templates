<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\ViewHelpers\GlobalVariables;

use Closure;
use Exception;
use PSBits\Foundation\ViewHelpers\GlobalVariables\AbstractGlobalVariablesViewHelper;
use PSBits\Templates\Service\GlobalVariableProviders\SettingsProvider;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class SettingsViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\GlobalVariables
 */
class SettingsViewHelper extends AbstractGlobalVariablesViewHelper
{
    /**
     * @throws Exception
     */
    public static function renderStatic(
        array                     $arguments,
        Closure                   $renderChildrenClosure,
        RenderingContextInterface $renderingContext,
    ): mixed {
        return parent::getVariable(SettingsProvider::class, $arguments);
    }
}
