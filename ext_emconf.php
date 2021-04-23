<?php
declare(strict_types=1);

$EM_CONF['psb_templates'] = [
    'author'           => 'Daniel Ablass',
    'author_email'     => 'dn@phantasie-schmiede.de',
    'category'         => 'misc',
    'clearCacheOnLoad' => true,
    'constraints'      => [
        'conflicts' => [
        ],
        'depends'   => [
            'psb_foundation' => '1.0.0',
        ],
        'suggests'  => [
            'fe_change_pwd' => '',
        ],
    ],
    'description'      => 'Basic templates for TYPO3',
    'state'            => 'alpha',
    'title'            => 'PSbits | Templates',
    'version'          => '0.0.0',
];
