<?php

namespace App\Factory;

use App\Entity\User;

class UserFactory
{
    public static function create(string $email): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);

        return $user;
    }
}
