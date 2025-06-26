<?php
declare(strict_types=1);

$EM_CONF['psb_templates'] = [
    'author'           => 'Daniel Ablass',
    'author_email'     => 'dn@phantasie-schmiede.de',
    'category'         => 'misc',
    'clearCacheOnLoad' => true,
    'constraints'      => [
        'conflicts' => [],
        'depends'   => [
            'foundation'       => '3.0',
            'fluid_styled_content' => '12.0.0-12.99.99',
            'ws_scss'              => '12.0.0-12.99.99',
        ],
    ],
    'description'      => 'Basic templates for TYPO3',
    'state'            => 'alpha',
    'title'            => 'PSBits | Templates',
    'version'          => '0.0.0',
];
