<?php

return [
    'basePath' => __DIR__,
    'components' => [
        'db' => [
            'class' => 'core\components\db\Database',
            'dsn' => 'mysql:host=127.0.0.1;dbname=framework;charset=utf8',
            'user' => 'root',
            'password' => ''
        ],
        'user' => [
            'class' => 'core\components\user\User',
            'identityClass' => 'app\models\User'
        ]
    ]
];