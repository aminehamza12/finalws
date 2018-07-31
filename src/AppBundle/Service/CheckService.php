<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;

class CheckService
{
    /**
     * Check soap service, display name when called
     * @param string $name
     * @return mixed
     */
    public function check($name)
    {
        return 'Hello '.$name;
    }
}