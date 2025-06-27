<?php

$EM_CONF[$_EXTKEY] = [
    'author'           => 'Daniel Ablass',
    'author_email'     => 'dn@phantasie-schmiede.de',
    'category'         => 'misc',
    'clearCacheOnLoad' => true,
    'constraints'      => [
        'conflicts' => [],
        'depends'   => [
            'foundation'           => '3.0',
            'fluid_styled_content' => '12.0.0-12.99.99',
            'php'                  => '8.3',
            'typo3'                => '13.4.0-13.4.99',
        ],
    ],
    'description'      => 'Basic templates for TYPO3',
    'state'            => 'alpha',
    'title'            => 'PSBits | Templates',
    'version'          => '0.1.0',
];
