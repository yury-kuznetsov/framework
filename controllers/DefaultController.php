<?php

namespace app\controllers;

use Core;
use core\base\Controller;

class DefaultController extends Controller
{
    /**
     * Renders a default page.
     *
     * @return mixed
     */
    public function index()
    {
        $username = 'Guest';
        if (!Core::$app->user->isGuest()) {
            $username = Core::$app->user->identity->{'login'};
        }

        return $this->asHtml('index', [
            'username' => $username
        ]);
    }

    /**
     * Renders a captcha.
     *
     * @return mixed
     */
    public function captcha()
    {
        $code = rand(10000, 99999);
        Core::$app->session->set('captcha', $code);

        $this->response->setHeader('Content-type', 'image/png');

        $image = imagecreatetruecolor(140, 46);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $font = Core::$app->getBasePath() . '/web/fonts/Roboto/Roboto-Black.ttf';

        imagefilledrectangle($image, 0, 0, 399, 99, $white);
        imagettftext($image, 30, 0, 13, 38, $black, $font, $code);
        imagepng($image);

        return $this->response;
    }
}