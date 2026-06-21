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
use PSBits\Foundation\Attribute\TCA\ColumnType\Input;
use PSBits\Foundation\Attribute\TCA\ColumnType\Select;
use PSBits\Foundation\Attribute\TCA\Mapping\Field;
use PSBits\Foundation\Attribute\TCA\Mapping\Table;
use PSBits\Templates\Enum\AiStatus;

/**
 * Class Page
 *
 * @package PSBits\Templates\Domain\Model\Typo3
 */
#[Table(name: 'sys_file_metadata')]
class SysFileMetaData
{
    #[Column(l10nMode: 'exclude', position: 'after:title')]
    #[Field(name: 'tx_templates_copyright')]
    #[Input]
    protected string $copyright = '';

    #[Column(l10nMode: 'exclude', position: 'after:tx_templates_copyright')]
    #[Field(name: 'tx_templates_ai_status')]
    #[Select(items: AiStatus::OPTIONS)]
    protected string $aiStatus = AiStatus::None->value;
}
