<?php

namespace app\controllers;

use app\models\forms\LoginForm;
use app\models\forms\RegisterForm;
use Core;
use core\base\Controller;
use core\components\Response;

class AccountController extends Controller
{
    /**
     * Creates new user.
     *
     * @return Response|mixed
     */
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

    /**
     * Updates user profile.
     *
     * @return mixed
     */
    public function profile()
    {
        $updated = false;

        $model = new ProfileForm();
        if ($model->load($_POST) && $model->update()) {
            $updated = true;
        }

        return $this->asHtml('profile', [
            'model' => $model,
            'updated' => $updated
        ]);

    }

    /**
     * Log In action.
     *
     * @return Response|mixed
     */
    public function login()
    {
        $model = new LoginForm();
        if ($model->load($_POST) && $model->login()) {
            return $this->redirect('?r=default/index');
        }

        $model->captcha = null;

        return $this->asHtml('login', [
            'model' => $model
        ]);
    }

    /**
     * Log Out action.
     *
     * @return Response
     */
    public function logout()
    {
        Core::$app->user->logout();
        return $this->redirect('?r=default/index');
    }
}