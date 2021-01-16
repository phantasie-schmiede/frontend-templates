<?php
$frontendUserFields = [];
$ll = 'LLL:EXT:psb_templates/Resources/Private/Language/Backend/Configuration/SiteConfiguration/Overrides/sites.xlf:';

$frontendUserFields[] = 'frontendUserPid';
$GLOBALS['SiteConfiguration']['site']['columns']['frontendUserPid'] = [
    'config'      => [
        'eval'        => 'int',
        'type'        => 'input',
    ],
    'description' => $ll . 'frontendUserPid.description',
    'label'       => $ll . 'frontendUserPid.label',
];

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('fe_change_pwd')) {
    $frontendUserFields[] = 'changePasswordPid';
    $GLOBALS['SiteConfiguration']['site']['columns']['changePasswordPid'] = [
        'config'      => [
            'eval'        => 'int',
            'type'        => 'input',
        ],
        'description' => $ll . 'changePasswordPid.description',
        'label'       => $ll . 'changePasswordPid.label',
    ];
}

$frontendUserFields[] = 'resetPasswordPid';
$GLOBALS['SiteConfiguration']['site']['columns']['resetPasswordPid'] = [
    'config'      => [
        'eval'        => 'int',
        'type'        => 'input',
    ],
    'description' => $ll . 'resetPasswordPid.description',
    'label'       => $ll . 'resetPasswordPid.label',
];

$GLOBALS['SiteConfiguration']['site']['columns']['websiteColor'] = [
    'config'      => [
        'renderType' => 'colorpicker',
        'size'       => 10,
        'type'       => 'input',
    ],
    'description' => $ll . 'websiteColor.description',
    'label'       => $ll . 'websiteColor.label',
];

$GLOBALS['SiteConfiguration']['site']['columns']['websiteLogo'] = [
    'config'      => [
        'eval'        => 'trim',
        'mode'        => 'useOrOverridePlaceholder',
        'placeholder' => 'fileadmin/user_upload/logo.svg',
        'type'        => 'input',
    ],
    'description' => $ll . 'websiteLogo.description',
    'label'       => $ll . 'websiteLogo.label',
];

$GLOBALS['SiteConfiguration']['site']['columns']['websiteSubtitle'] = [
    'config'      => [
        'eval' => 'trim',
        'type' => 'input',
    ],
    'description' => $ll . 'websiteSubtitle.description',
    'label'       => $ll . 'websiteSubtitle.label',
];

$GLOBALS['SiteConfiguration']['site']['palettes']['default']['showitem'] .= ', websiteSubtitle';
$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ', --div--;Design, websiteLogo, websiteColor, --div--;FE-Login, ' . implode(', ', $frontendUserFields);

