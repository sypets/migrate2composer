<?php


$EM_CONF[$_EXTKEY] = [
    'title' => 'Migrate to Composer',
    'description' => 'This extension dumps information about currently loaded extensions to screen to be used as composer.json or to run composer commands.',
    'category' => '',
    'author' => 'Sybille Peters',
    'author_email' => 'sypets@gmx.de',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '10.0.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.38-10.4.999'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

