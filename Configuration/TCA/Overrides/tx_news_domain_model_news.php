<?php
declare(strict_types=1);

use PSBits\Templates\Utility\Component\CardUtility;

if (!isset($GLOBALS['TCA']['tx_news_domain_model_news']['columns']['fal_media'])) {
    return;
}

$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['fal_media']['config']['overrideChildTca']['columns']['crop']['config']['cropVariants'] = CardUtility::getTcaCropVariants(
);
