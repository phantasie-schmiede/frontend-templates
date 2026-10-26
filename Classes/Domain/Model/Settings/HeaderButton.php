<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Domain\Model\Settings;

use PSBits\Foundation\Attribute\TCA\Column;
use PSBits\Foundation\Attribute\TCA\ColumnType\Enum;
use PSBits\Foundation\Attribute\TCA\ColumnType\Input;
use PSBits\Foundation\Attribute\TCA\ColumnType\Link;
use PSBits\Foundation\Attribute\TCA\Ctrl;
use PSBits\Templates\Enum\BootstrapType;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class HeaderButton
 *
 * @package PSBits\Templates\Domain\Model\Settings
 */
#[Ctrl(ignorePageTypeRestriction: true, label: 'label', labelAlt: 'type', labelAltForce: true)]
class HeaderButton extends AbstractEntity
{
    #[Column(required: true)]
    #[Input]
    protected string $label = '';

    #[Column(required: true)]
    #[Link]
    protected string $link = '';

    #[Column(required: true)]
    #[Enum(BootstrapType::class)]
    protected BootstrapType $type = BootstrapType::Primary;

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getType(): BootstrapType
    {
        return $this->type;
    }
}
