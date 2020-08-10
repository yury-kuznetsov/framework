<?php

namespace core\components;

use core\base\Component;

class Session extends Component
{
    /**
     * @var bool
     */
    public $isActive;


    /**
     * Starts a session if its not open.
     */
    public function start()
    {
        if ($this->isActive) {
            return;
        }

        session_start();
        $this->isActive = true;

        return;
    }

    /**
     * Sets a session value.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    /**
     * Gets a session value.
     *
     * @param $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        $this->start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Removes a session value.
     *
     * @param string $key
     */
    public function remove($key)
    {
        $this->start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
}