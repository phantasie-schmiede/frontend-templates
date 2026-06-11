<?php

declare(strict_types=1);

use PSBits\Templates\Utility\Component\CardUtility;

if (!isset($GLOBALS['TCA']['tx_calendarize_domain_model_event']['columns']['images'])) {
    return;
}

$GLOBALS['TCA']['tx_calendarize_domain_model_event']['columns']['images']['config']['overrideChildTca']['columns']['crop']['config']['cropVariants'] = CardUtility::getTcaCropVariants(
);
