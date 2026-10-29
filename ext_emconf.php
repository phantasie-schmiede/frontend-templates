<?php

$EM_CONF[$_EXTKEY] = [
    'author'           => 'Daniel Ablass',
    'author_email'     => 'dn@phantasie-schmiede.de',
    'category'         => 'misc',
    'clearCacheOnLoad' => true,
    'constraints'      => [
        'conflicts' => [],
        'depends'   => [
            'foundation'           => '4.2',
            'fluid_styled_content' => '13.0.0-14.99.99',
            'php'                  => '8.3.0-8.5.99',
            'typo3'                => '13.4.0-14.99.99',
        ],
    ],
    'description'      => 'Frontend templates for TYPO3: page layouts, content elements, ViewHelpers and TypoScript setup, built on Bootstrap and Content Blocks',
    'state'            => 'alpha',
    'title'            => 'PSBits | Frontend Templates',
    'version'          => '0.1.0',
];
