<?php
declare(strict_types=1);
namespace PSB\PsbTemplates\ViewHelpers;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Class SvgSpriteViewHelper
 *
 * @package PSB\PsbTemplates\ViewHelpers
 */
class SvgSpriteViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * Name of the tag to be created by this view helper
     *
     * @var string
     * @api
     */
    protected $tagName = 'svg';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('color', 'string', 'color of the icon', false);
        $this->registerArgument('height', 'int', 'height of the icon', false, 32);
        $this->registerArgument('icon', 'string', 'name of the icon', true);
        $this->registerArgument('width', 'int', 'width of the icon', false, 32);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $this->tag->addAttributes([
            'height' => $this->arguments['height'],
            'width'  => $this->arguments['width'],
        ]);

        if (!empty($this->arguments['color'])) {
            $this->tag->addAttribute('style', 'color: ' . $this->arguments['color'] . ';');
        }

        $filePath = GeneralUtility::getFileAbsFileName('EXT:psb_templates/Resources/Public/Icons/Vendors/bootstrap-icons-1.3.0/bootstrap-icons.svg');
        $filePath = PathUtility::getAbsoluteWebPath($filePath);
        $this->tag->setContent('<use xlink:href="' . $filePath . '#' . $this->arguments['icon'] . '"/>');

        return $this->tag->render();
    }
}
