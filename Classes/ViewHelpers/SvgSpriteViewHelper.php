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

namespace PSB\PsbTemplates\ViewHelpers;

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
        $this->registerArgument('color', 'string', 'color of the icon');
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
