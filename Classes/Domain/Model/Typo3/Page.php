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

namespace PSB\PsbTemplates\Domain\Model\Typo3;

use PSB\PsbFoundation\Annotation\TCA\Column\Select;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Page
 *
 * @package PSB\PsbTemplates\Domain\Model\Typo3
 */
class Page extends AbstractEntity
{
    public const BOOTSTRAP_CONTAINER_TYPES = [
        'DEFAULT' => '',
        'SM'      => 'sm',
        'MD'      => 'md',
        'LG'      => 'lg',
        'XL'      => 'xl',
        'XXL'     => 'xxl',
        'FLUID'   => 'fluid',
    ];

    /**
     * @var string
     * @Select(items=self::BOOTSTRAP_CONTAINER_TYPES, position="after:layout")
     */
    protected string $bootstrapContainerType = self::BOOTSTRAP_CONTAINER_TYPES['DEFAULT'];
}
