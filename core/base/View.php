<?php

namespace core\base;

use Core;

class View extends Component
{
    /**
     * @var Controller
     */
    public $controller;
    /**
     * @var string
     */
    public $viewPath;


    /**
     * View constructor.
     *
     * @param Controller $controller
     * @param array      $config
     */
    public function __construct($controller, $config = [])
    {
        $this->controller = $controller;
        parent::__construct($config);
    }

    /**
     * Initializes the view.
     */
    public function init()
    {
        parent::init();
        $this->viewPath = Core::$app->getBasePath() . DIRECTORY_SEPARATOR .
            'views' . DIRECTORY_SEPARATOR . $this->controller->id;
    }

    /**
     * Renders template with parameters.
     *
     * @param string $template
     * @param array  $params
     *
     * @return false|string
     */
    public function render($template, $params)
    {
        $file = $this->viewPath . DIRECTORY_SEPARATOR . $template . '.php';
        return $this->renderFile($file, $params);
    }

    /**
     * Renders file with parameters.
     *
     * @param string $file
     * @param array  $params
     *
     * @return false|string
     */
    private function renderFile($file, $params)
    {
        ob_start();
        extract($params, EXTR_OVERWRITE);
        require $file;

        return ob_get_clean();
    }
}