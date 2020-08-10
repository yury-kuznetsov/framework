# Simple PHP framework
Простая реализация самописного PHP-фреймворка

### Установка
Перед началом работы необходимо указать настройки для подключения к БД в файле `config.php`, 
а также создать структуру таблиц с помощью файла `structure.sql`.

Старт приложения происходит из папки web.

### Принцип работы
Из адресной строки выбирается параметр `?r=account/create`. 
С помощью DI-контейнера строится объект `app\controllers\AccountController`. 
Из построенного объекта вызывается метод `create()`.

### Контроллеры
Вся бизнес-логика расписывается в моделях, из-за чего контроллеры реализованы минимумом кода.
```php
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
```

### Модели
Модель содержит список правил для валидации входных данных.
```php
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
```

### Active Record
Шаблон Active Record позволяет упростить работу с базой данных.
```php
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
```