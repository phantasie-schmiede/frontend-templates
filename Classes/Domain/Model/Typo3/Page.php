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
    public const ROW_HIGHLIGHT_TOKENS = [
        'NONE'     => 'none',
        'SUBTLE'   => 'subtle',
        'EMPHASIS' => 'emphasis',
    ];

    #[Column(position: 'after:layout')]
    #[Field(name: 'tx_templates_bs_container_type')]
    #[Select(items: self::BOOTSTRAP_CONTAINER_TYPES)]
    protected string $bootstrapContainerType = self::BOOTSTRAP_CONTAINER_TYPES['DEFAULT'];

    #[Column(default: self::ROW_HIGHLIGHT_TOKENS['NONE'], position: 'after:tx_templates_bs_container_type')]
    #[Field(name: 'tx_templates_row_highlight_1')]
    #[Select(items: self::ROW_HIGHLIGHT_TOKENS)]
    protected string $rowHighlight1 = self::ROW_HIGHLIGHT_TOKENS['NONE'];

    #[Column(default: self::ROW_HIGHLIGHT_TOKENS['NONE'], position: 'after:tx_templates_row_highlight_1')]
    #[Field(name: 'tx_templates_row_highlight_2')]
    #[Select(items: self::ROW_HIGHLIGHT_TOKENS)]
    protected string $rowHighlight2 = self::ROW_HIGHLIGHT_TOKENS['NONE'];

    #[Column(default: self::ROW_HIGHLIGHT_TOKENS['NONE'], position: 'after:tx_templates_row_highlight_2')]
    #[Field(name: 'tx_templates_row_highlight_3')]
    #[Select(items: self::ROW_HIGHLIGHT_TOKENS)]
    protected string $rowHighlight3 = self::ROW_HIGHLIGHT_TOKENS['NONE'];

    #[Column(default: self::ROW_HIGHLIGHT_TOKENS['NONE'], position: 'after:tx_templates_row_highlight_3')]
    #[Field(name: 'tx_templates_row_highlight_4')]
    #[Select(items: self::ROW_HIGHLIGHT_TOKENS)]
    protected string $rowHighlight4 = self::ROW_HIGHLIGHT_TOKENS['NONE'];
}
