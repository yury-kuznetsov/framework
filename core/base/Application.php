<?php

namespace core\base;

use Core;
use core\components\db\Database;
use core\components\Request;
use core\components\Response;
use core\components\Session;
use core\components\user\User;

/**
 * Class Application
 *
 * @property Database $db
 * @property Request  $request
 * @property Response $response
 * @property Session  $session
 * @property User     $user
 * @package core\base
 */
class Application extends Container
{
    /**
     * @var string
     */
    public $defaultRoute = 'default';
    /**
     * @var string
     */
    public $controllerNamespace = 'app\\controllers';

    /**
     * @var string
     */
    private $_basePath;


    /**
     * Application constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        Core::$app = $this;
        $this->prepareConfig($config);
        parent::__construct($config);
    }

    /**
     * Launches the application.
     */
    public function run()
    {
        $response = $this->handleRequest($this->getRequest());
        $response->send();
    }

    /**
     * Handles the request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handleRequest($request)
    {
        list($route, $params) = $request->resolve();
        $result = $this->runAction($route, $params);

        if ($result instanceof Response) {
            return $result;
        }

        $response = $this->getResponse();
        $response->data = $result;

        return $response;
    }

    /**
     * Launches the action.
     *
     * @param string $route
     * @param array  $params
     *
     * @return mixed
     */
    private function runAction($route, $params)
    {
        list($controller, $actionId) = $this->createController($route);
        return $controller->runAction($actionId, $params);
    }

    /**
     * Parses the route and return controller and action.
     *
     * @param $route
     *
     * @return array
     */
    private function createController($route)
    {
        if ($route === '') {
            $route = $this->defaultRoute;
        }

        $route = trim($route, '/');
        list($id, $actionId) = explode('/', $route);

        $controller = $this->createControllerById($id);

        return [$controller, $actionId];
    }

    /**
     * Creates controller by ID.
     *
     * @param $id
     *
     * @return object
     */
    private function createControllerById($id)
    {
        $className = ucfirst($id) . 'Controller';
        $className = ltrim(
            $this->controllerNamespace . '\\' . $className,
            '\\'
        );

        return $this->make($className, ['id' => $id]);
    }

    /**
     * Prepares application configuration.
     *
     * @param $config
     */
    private function prepareConfig(&$config)
    {
        $this->setBasePath($config['basePath']);
        unset($config['basePath']);

        foreach ($this->components() as $id => $component) {
            $config['components'][$id] = $component;
        }
    }

    /**
     * Sets base path of application.
     *
     * @param $basePath
     */
    public function setBasePath($basePath)
    {
        $this->_basePath = $basePath;
        Core::setAlias('@app', $basePath);
    }

    /**
     * Returns base path of application.
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Returns application default components.
     *
     * @return array
     */
    private function components()
    {
        return [
            'request'  => ['class' => 'core\components\Request'],
            'response' => ['class' => 'core\components\Response'],
            'session'  => ['class' => 'core\components\Session'],
        ];
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->get('session');
    }

    /**
     * @return Session
     */
    public function getUser()
    {
        return $this->get('user');
    }
}