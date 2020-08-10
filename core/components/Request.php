<?php

namespace core\components;

use core\base\Component;

class Request extends Component
{
    public $routeParam = 'r';


    public function resolve()
    {
        $route = $this->getParam($this->routeParam, '');
        if (isset($_GET[$this->routeParam])) {
            unset($_GET[$this->routeParam]);
        }

        return [$route, $_GET];
    }

    public function getParam($name, $defaultValue = null)
    {
        $params = $_GET;

        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }
}