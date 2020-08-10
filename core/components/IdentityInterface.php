<?php

namespace core\components;

interface IdentityInterface
{
    public static function identityById($id);

    public function getId();
}