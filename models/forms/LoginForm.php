<?php

namespace app\models\forms;

use app\models\User;
use Core;
use core\base\Model;

class LoginForm extends Model
{
    /**
     * @var string
     */
    public $login;
    /**
     * @var string
     */
    public $password;
    /**
     * @var string
     */
    public $captcha;

    /**
     * Rules of validation.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['login', 'password', 'captcha'], 'required'],
            [['login'], 'email'],
            [['captcha'], 'captcha'],
        ];
    }

    /**
     * Log In
     *
     * @return bool
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::find(['`login` = "' . $this->login . '"']);
        if (is_null($user)) {
            $this->setError('login', 'User does not exists');
            return false;
        }

        if (!password_verify($this->password, $user->password)) {
            $this->setError('password', 'Incorrect password');
            return false;
        }

        Core::$app->user->login($user);

        return true;
    }
}