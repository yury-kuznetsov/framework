<?php

namespace core\components;

use Core;
use core\base\ActiveRecord;
use core\base\Component;

/**
 * Class User
 *
 * @property IdentityInterface $identity
 * @package core\components
 */
class User extends Component
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var IdentityInterface
     */
    protected $_identity;
    /**
     * @var string
     */
    protected $_identityClass;


    /**
     * User constructor.
     *
     * @param string $identityClass
     * @param array  $config
     */
    public function __construct($identityClass, $config = [])
    {
        $this->_identityClass = $identityClass;
        parent::__construct($config);
    }

    /**
     * Initializes the component.
     */
    public function init()
    {
        parent::init();
        $this->id = Core::$app->session->get('identity_id');
    }

    /**
     * Log In action.
     *
     * @param IdentityInterface $user
     *
     * @return bool
     */
    public function login(IdentityInterface $user)
    {
        $this->id = $user->getId();
        $this->_identity = $user;
        Core::$app->session->set('identity_id', $this->id);

        return true;
    }

    /**
     * Log Out action.
     *
     * @return bool
     */
    public function logout()
    {
        $this->id = null;
        $this->_identity = null;
        Core::$app->session->remove('identity_id');

        return true;
    }

    /**
     * @return bool
     */
    public function isGuest()
    {
        return $this->id === null;
    }

    /**
     * Returns a identity.
     *
     * @return IdentityInterface
     */
    public function getIdentity()
    {
        if ($this->id === null) {
            return null;
        }

        if ($this->_identity !== null) {
            return $this->_identity;
        }

        /* @var $class IdentityInterface */
        $class = $this->_identityClass;
        $identity = $class::identityById($this->id);

        return $this->_identity = $identity;
    }
}