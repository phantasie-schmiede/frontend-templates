<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Domain\Model\Typo3;

use PSBits\Foundation\Attribute\TCA\Column;
use PSBits\Foundation\Attribute\TCA\ColumnType\Enum;
use PSBits\Foundation\Attribute\TCA\ColumnType\Select;
use PSBits\Foundation\Attribute\TCA\Mapping\Field;
use PSBits\Foundation\Attribute\TCA\Mapping\Table;
use PSBits\Templates\Enum\RowHighlight;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Page
 *
 * @package PSBits\Templates\Domain\Model\Typo3
 */
#[Table(name: 'pages')]
class Page extends AbstractEntity
{
    public const array   BOOTSTRAP_CONTAINER_TYPES = [
        'DEFAULT' => '',
        'SM'      => 'sm',
        'MD'      => 'md',
        'LG'      => 'lg',
        'XL'      => 'xl',
        'XXL'     => 'xxl',
        'FLUID'   => 'fluid',
    ];
    private const string DISPLAY_COND_PREFIX = 'USER:PSBits\Templates\Backend\DisplayCondition\PageRowHighlightDisplayCondition->matches:';

    #[Column(position: 'after:layout')]
    #[Field(name: 'tx_templates_bs_container_type')]
    #[Select(items: self::BOOTSTRAP_CONTAINER_TYPES)]
    protected string $bootstrapContainerType = self::BOOTSTRAP_CONTAINER_TYPES['DEFAULT'];

    #[Column(databaseDefinition: 'varchar(8) DEFAULT NULL', default: RowHighlight::None->value, displayCond: self::DISPLAY_COND_PREFIX . '1', l10nMode: 'exclude', position: 'after:backend_layout_next_level')]
    #[Enum(enumClass: RowHighlight::class)]
    #[Field(name: 'tx_templates_row_highlight_1')]
    protected RowHighlight $rowHighlight1 = RowHighlight::None;

    #[Column(databaseDefinition: 'varchar(8) DEFAULT NULL', default: RowHighlight::None->value, displayCond: self::DISPLAY_COND_PREFIX . '2', l10nMode: 'exclude', position: 'after:tx_templates_row_highlight_1')]
    #[Enum(enumClass: RowHighlight::class)]
    #[Field(name: 'tx_templates_row_highlight_2')]
    protected RowHighlight $rowHighlight2 = RowHighlight::None;

    #[Column(databaseDefinition: 'varchar(8) DEFAULT NULL', default: RowHighlight::None->value, displayCond: self::DISPLAY_COND_PREFIX . '3', l10nMode: 'exclude', position: 'after:tx_templates_row_highlight_2')]
    #[Enum(enumClass: RowHighlight::class)]
    #[Field(name: 'tx_templates_row_highlight_3')]
    protected RowHighlight $rowHighlight3 = RowHighlight::None;
}
