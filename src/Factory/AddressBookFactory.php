<?php

namespace App\Factory;

use App\Entity\AddressBook;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AddressBookFactory
{
    public static function create(array $properties): AddressBook
    {
        $addressBook = new AddressBook();

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($properties as $property => $value) {
            $propertyAccessor->setValue($addressBook, $property, $value);
        }

        return $addressBook;
    }
}
