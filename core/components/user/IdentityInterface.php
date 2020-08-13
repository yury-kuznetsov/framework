<?php

namespace core\components\user;

interface IdentityInterface
{
    public static function identityById($id);

    public function getId();
}