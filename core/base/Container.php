<?php

namespace core\base;

use ReflectionClass;
use ReflectionException;

class Container extends Component
{
    /**
     * @var array components (singletons)
     */
    protected $_components;
    /**
     * @var array definitions of the components
     */
    protected $_definitions;


    /**
     * Getter magic method.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        if ($this->has($id)) {
            return $this->get($id);
        }
        return parent::__get($id);
    }

    /**
     * Makes new instance object by class name with parameters.
     *
     * @param string $class
     * @param array  $params
     *
     * @return mixed
     */
    public function make($class, $params = []) {
        $args = [];
        try {
            $reflection = new ReflectionClass($class);
            $constructor = $reflection->getConstructor();
            if ($constructor !== null) {
                foreach ($constructor->getParameters() as $param) {
                    if (isset($params[$param->name])) {
                        $args[$param->name] = $params[$param->name];
                    } else {
                        if ($param->isDefaultValueAvailable()) {
                            $args[$param->name] = $param->getDefaultValue();
                        } elseif (($paramClass = $param->getClass()->name) !== null) {
                            $args[$param->name] = $this->make($paramClass);
                        }
                    }
                }
            }
        } catch (ReflectionException $e) {
            return null;
        }

        return $reflection->newInstanceArgs($args);
    }

    /**
     * Returns an instance of the requested component ID.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function get($id)
    {
        if (isset($this->_components[$id])) {
            return $this->_components[$id];
        }

        $definition = $this->_definitions[$id];
        $class = $definition['class'];
        unset($definition['class']);

        return $this->_components[$id] = $this->make($class, $definition);
    }

    /**
     * Sets component definition.
     * For example,
     *
     * ```php
     *
     * $container->set('mailer', [
     *  'class' => 'app\components\Mailer',
     *  'param1' => 'value1',
     *  'param2' => 'value2',
     * ]);
     *
     * ```
     *
     * @param string $id
     * @param array  $definition
     */
    public function set($id, $definition)
    {
        $this->_definitions[$id] = $definition;
    }

    /**
     * Checks for a component definition.
     *
     * @param $id
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->_definitions[$id]);
    }

    /**
     * Sets component definitions.
     *
     * @param array $components
     */
    public function setComponents($components)
    {
        foreach ($components as $id => $definition) {
            $this->set($id, $definition);
        }
    }
}