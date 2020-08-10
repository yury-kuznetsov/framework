<?php

/* @var $username string */

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <title>Home page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/styles/default.min.css">
</head>
<body>

<div class="home pt-md-5 pb-md-5 text-center">

    <? if (Core::$app->user->isGuest()) { ?>

        <p class="lead">
            Hi, <?= $username ?>! <br/>
            <a href="?r=account/login">login</a> | <a href="?r=account/create">register</a>
        </p>

    <? } else { ?>

        <p class="lead">
            Hi, <?= $username ?>! <br/>
            <a href="?r=account/logout">logout</a>
        </p>

    <? } ?>

    <h1 class="display-4 pt-5">Simple PHP framework</h1>
    <p class="lead">Простая реализация самописного PHP фреймворка</p>
</div>

<div class="border-top">
    <div class="container">
        <div class="pt-5">
            <p class="h3">Принцип работы</p>
            <p>
                Из адресной строки выбирается параметр <code>?r=account/create</code>.
                С помощью DI-контейнера строится объект <code>app\controllers\AccountController</code>.
                Из построенного объекта вызывается метод <code>create()</code>.
            </p>
        </div>

        <div class="pt-3">
            <p class="h3">Контроллеры</p>
            <p>
                Вся бизнес-логика расписывается в моделях, из-за чего контроллеры реализованы минимумом кода.
            </p>
            <pre>
                <code class="language-php">
    public function create()
    {
        $model = new RegisterForm();
        if ($model->load($_POST) && $model->register()) {
            return $this->redirect('?r=default/index');
        }

        $model->captcha = null;

        return $this->asHtml('create', [
            'model' => $model
        ]);
    }
                </code>
            </pre>
        </div>

        <div class="pt-3">
            <p class="h3">Модели</p>
            <p>
                Модель содержит список правил для валидации входных данных.
            </p>
            <pre>
                <code class="language-php">
    public function rules()
    {
        return [
            [['login', 'password', 'passwordRepeat', 'captcha'], 'required'],
            [['login'], 'email'],
            [['login'], 'alreadyRegister'],
            [['password'], 'repeat', ['to' => 'passwordRepeat']],
            [['password'], 'passport'],
            [['captcha'], 'captcha'],
        ];
    }
                </code>
            </pre>
        </div>

        <div class="pt-3">
            <p class="h3">Active Record</p>
            <p>
                Шаблон Active Record позволяет упростить работу с базой данных.
            </p>
            <pre>
                <code class="language-php">
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->login = $this->login;
        $user->password = password_hash($this->password, PASSWORD_DEFAULT);
        $user->save();

        if ($user->id > 0) {
            Core::$app->user->login($user);

            return true;
        }

        return false;
    }
                </code>
            </pre>
        </div>
    </div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
</body>
</html>

