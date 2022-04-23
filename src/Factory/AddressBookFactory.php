<?php

namespace App\Factory;

use App\Entity\AddressBook;
use App\Entity\User;

class AddressBookFactory
{
    public static function create(User $user, string $fistName, ?string $lastName, ?string $email, ?string $phone): AddressBook
    {
        $addressBook = new AddressBook();
        $addressBook->setUser($user);
        $addressBook->setFirstName($fistName);
        $addressBook->setLastName($lastName);
        $addressBook->setEmail($email);
        $addressBook->setPhone($phone);

        return $addressBook;
    }
}
