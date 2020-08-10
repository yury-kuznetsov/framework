<?php

return [
    'basePath' => __DIR__,
    'components' => [
        'db' => [
            'class' => 'core\components\Database',
            'dsn' => 'mysql:host=127.0.0.1;dbname=phone_book;charset=utf8',
            'user' => 'root',
            'password' => ''
        ],
        'user' => [
            'class' => 'core\components\User',
            'identityClass' => 'app\models\User'
        ]
    ]
];