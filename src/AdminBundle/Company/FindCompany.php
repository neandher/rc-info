<?php
/**
 * Created by PhpStorm.
 * User: neand
 * Date: 08/07/2017
 * Time: 17:03
 */

namespace AdminBundle\Company;


use AdminBundle\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;

class FindCompany
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * FindCompany constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Company|bool
     */
    public function find()
    {
        $companys = $this->em->getRepository(Company::class)->findAll();

        if (!count($companys) > 0) {
            return false;
        }

        return $companys[0];
    }
}