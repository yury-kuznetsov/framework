<?php

namespace app\models\forms;

use app\models\User;
use Core;
use core\base\Model;

class RegisterForm extends Model
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
    public $passwordRepeat;
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
            [['login', 'password', 'passwordRepeat', 'captcha'], 'required'],
            [['login'], 'email'],
            [['login'], 'alreadyRegister'],
            [['password'], 'repeat', ['to' => 'passwordRepeat']],
            [['password'], 'passport'],
            [['captcha'], 'captcha'],
        ];
    }

    /**
     * Checks for user existence with such email.
     *
     * @param string $attribute
     */
    public function validateAlreadyRegister($attribute)
    {
        $user = User::find(['`login` = "' . $this->login . '"']);
        if ($user !== null) {
            $this->setError($attribute, 'User already exists');
        }
    }

    /**
     * Checks the password.
     * Password must contain letters and numbers.
     *
     * @param string $attribute
     */
    public function validatePassport($attribute)
    {
        $numbers = preg_replace("/[^\d]/", '', $this->{$attribute});
        if (strlen($numbers) === 0) {
            $this->setError($attribute, 'Must contain numbers');
        }

        $letters = preg_replace("/[^a-zA-Z]/", '', $this->{$attribute});
        if (strlen($letters) === 0) {
            $this->setError($attribute, 'Must contain letters');
        }
    }

    /**
     * Registers new user.
     *
     * @return integer|bool
     */
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
}