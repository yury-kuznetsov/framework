<?php

namespace core\base;

use Core;
use core\components\Response;

class Controller extends Component
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $defaultAction = 'index';
    /**
     * @var Response
     */
    public $response;


    /**
     * Controller constructor.
     *
     * @param string $id
     * @param array  $config
     */
    public function __construct($id, $config = [])
    {
        $this->id = $id;
        parent::__construct($config);
    }

    /**
     * Initializes the controller.
     */
    public function init()
    {
        parent::init();
        $this->response = Core::$app->response;
    }

    /**
     * Runs action with the passed parameters.
     *
     * @param string $actionId
     * @param array  $params
     *
     * @return mixed|null
     */
    public function runAction($actionId, $params)
    {
        if (empty($actionId)) {
            $actionId = $this->defaultAction;
        }

        if (method_exists($this, $actionId)) {
            if ($this->beforeAction($actionId, $params)) {
                $result = call_user_func_array([$this, $actionId], $params);
                $this->afterAction($actionId, $params);

                return $result;
            }

            return null;
        }

        return null;
    }

    /**
     * This method is called before action.
     *
     * @param string $actionId
     * @param array  $params
     *
     * @return bool
     */
    public function beforeAction($actionId, $params)
    {
        return true;
    }

    /**
     * This method is called after action.
     *
     * @param string $actionId
     * @param array  $params
     */
    public function afterAction($actionId, $params)
    {
    }

    /**
     * Redirects the browser to the URL.
     *
     * @param string $url
     * @param int    $statusCode
     *
     * @return Response
     */
    public function redirect($url, $statusCode = 302)
    {
        return $this->response->redirect($url, $statusCode);
    }

    /**
     * Renders content as HTML.
     *
     * @param string $template
     * @param array  $params
     *
     * @return mixed
     */
    public function asHtml($template, $params = [])
    {
        $view = Core::$app->make('core\base\View', [
            'controller' => $this
        ]);

        return $view->render($template, $params);
    }

    /**
     * Renders content as JSON.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function asJson($data)
    {
        $this->response->format = Response::FORMAT_JSON;
        $this->response->data = $data;

        return $this->response;
    }
}