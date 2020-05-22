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
            'fluid_styled_content' => '10.4.2-10.4.99',
            'php'                  => '7.4',
            'psb_foundation'       => '',
            'typo3'                => '10.4.2-10.4.99',
        ],
        'suggests'  => [
        ],
    ],
    'createDirs'       => '',
    'description'      => 'Basic templates for TYPO3',
    'state'            => 'alpha',
    'title'            => 'PSbits | Templates',
    'uploadfolder'     => false,
    'version'          => '0.0.0',
];
