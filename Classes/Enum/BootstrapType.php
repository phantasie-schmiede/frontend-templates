<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Enum;

/**
 * Class BootstrapType
 *
 * @package PSBits\FrontendTemplates\Enum
 */
enum BootstrapType: string
{
    case Danger    = 'danger';
    case Info      = 'info';
    case Primary   = 'primary';
    case Secondary = 'secondary';
    case Success   = 'success';
    case Warning   = 'warning';
}
