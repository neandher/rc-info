<?php

namespace AppBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class Loader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(
            [
                __DIR__ . '/site_user.yml',
                //__DIR__ . '/admin_user.yml',
            ],
            $manager,
            [
                'providers' => [$this]
            ]
        );
    }

}