<?php
return [
    'connections' => [
        'value' => [
            'default' => [
                'host' => 'MySQL-8.0',
                'database' => 'groupsix',
                'login' => 'root',
                'password' => ''
            ],
            'dev' => [
                'host' => 'localhost',
                'database' => '',
                'login' => 'root',
                'password' => ''
            ]
        ]
    ],
    'cache_flags'=> [
        'value' => [
            'cache_position' => $_SERVER['DOCUMENT_ROOT'] . '/core/cache/',
            'config_options' => 3600,
            'site_domain' => 3600
        ],
        'readonly' => false
    ]
];
