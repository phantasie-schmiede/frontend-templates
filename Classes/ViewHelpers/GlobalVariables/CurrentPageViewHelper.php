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
use PSBits\Templates\Service\GlobalVariableProviders\CurrentPageProvider;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class CurrentPageViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\GlobalVariables
 */
class CurrentPageViewHelper extends AbstractGlobalVariablesViewHelper
{
    /**
     * @param array<string, mixed> $arguments
     *
     * @throws Exception
     */
    public static function renderStatic(
        array                     $arguments,
        Closure                   $renderChildrenClosure,
        RenderingContextInterface $renderingContext,
    ): mixed {
        return parent::getVariable(CurrentPageProvider::class, $arguments);
    }
}
