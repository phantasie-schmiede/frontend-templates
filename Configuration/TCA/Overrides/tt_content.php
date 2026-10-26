<?php

declare(strict_types=1);

use PSBits\Foundation\Utility\Configuration\FilePathUtility;
use PSBits\FrontendTemplates\Data\ExtensionInformation;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

ExtensionManagementUtility::addTcaSelectItemGroup(
    'tt_content',
    'CType',
    'content_blocks',
    FilePathUtility::getLanguageFilePathForCurrentFile(
        GeneralUtility::makeInstance(
            ExtensionInformation::class
        )
    ) . 'group.contentBlocks.label'
);
