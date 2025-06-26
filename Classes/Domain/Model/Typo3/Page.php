<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Domain\Model\Typo3;

use PSBits\Foundation\Attribute\TCA\Column;
use PSBits\Foundation\Attribute\TCA\ColumnType\Select;
use PSBits\Foundation\Attribute\TCA\Mapping\Field;
use PSBits\Foundation\Attribute\TCA\Mapping\Table;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Page
 *
 * @package PSBits\Templates\Domain\Model\Typo3
 */
#[Table(name: 'pages')]
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

    #[Column(position: 'after:layout')]
    #[Field(name: 'tx_templates_bs_container_type')]
    #[Select(items: self::BOOTSTRAP_CONTAINER_TYPES)]
    protected string $bootstrapContainerType = self::BOOTSTRAP_CONTAINER_TYPES['DEFAULT'];
}
