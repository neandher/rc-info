<?php

namespace SiteBundle\Doctrine;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use SiteBundle\Entity\Customer;
use SiteBundle\Model\SiteUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Configurator
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var TokenStorageInterface
     */
    private $token;
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * Configurator constructor.
     * @param EntityManager $em
     * @param TokenStorageInterface $token
     * @param Reader $reader
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(
        EntityManager $em,
        TokenStorageInterface $token,
        Reader $reader,
        AuthorizationChecker $authorizationChecker
    )
    {
        $this->em = $em;
        $this->reader = $reader;
        $this->token = $token;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onKernelRequest()
    {
        if (($user = $this->getUser()) && $this->authorizationChecker->isGranted('ROLE_USER')) {
            /** @var Customer $customer */
            if ($customer = $user->getCustomer()) {
                $filter = $this->em->getFilters()->enable('portal_customer_filter');
                $filter->setParameter('id', $customer->getId());
                $filter->setAnnotationReader($this->reader);
            }
        }
    }

    private function getUser()
    {
        $token = $this->token->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        if (!($user instanceof SiteUserInterface)) {
            return null;
        }

        return $user;
    }
}