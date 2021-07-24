<?php

namespace App\ClassFile;

use Doctrine\ORM\Mapping\Entity;

class CheckUserOwner 
{

    public static function CheckOwnerShip($entity, $username)
    {
        if($entity->getUser()->getName() != $username)
            return true;
        

        return false;
    }
}