<?php
declare(strict_types=1);

/*
 * This file is part of PSB Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSB\PsbTemplates\Domain\Model\Typo3;

use PSB\PsbFoundation\Attribute\TCA\Column;
use PSB\PsbFoundation\Attribute\TCA\ColumnType\Select;
use PSB\PsbFoundation\Attribute\TCA\Mapping\Field;
use PSB\PsbFoundation\Attribute\TCA\Mapping\Table;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Page
 *
 * @package PSB\PsbTemplates\Domain\Model\Typo3
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

    /**
     * @var string
     */
    #[Column(position: 'after:layout')]
    #[Field(name: 'tx_psbtemplates_bs_container_type')]
    #[Select(items: self::BOOTSTRAP_CONTAINER_TYPES)]
    protected string $bootstrapContainerType = self::BOOTSTRAP_CONTAINER_TYPES['DEFAULT'];
}
