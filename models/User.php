<?php

namespace app\models;

use core\base\ActiveRecord;
use core\components\IdentityInterface;

/**
 * Class User
 *
 * @property integer $id
 * @property string  $login
 * @property string  $password
 * @package app\models
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @var string
     */
    static $table = 'user';


    /**
     * Identities by ID.
     *
     * @param $id
     *
     * @return User|null
     */
    public static function identityById($id)
    {
        return static::find($id);
    }

    /**
     * Returns the ID of current model.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}