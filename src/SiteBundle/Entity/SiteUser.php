<?php

namespace SiteBundle\Entity;

use SiteBundle\Model\SiteUserInterface;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="SiteBundle\Repository\SiteUserRepository")
 * @ORM\Table(name="site_user")
 */
class SiteUser extends BaseUser implements SiteUserInterface
{
    /**
     * @var Customer
     *
     * @ORM\OneToOne(targetEntity="SiteBundle\Entity\Customer", inversedBy="siteUser", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    protected $customer;

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return SiteUser
     */
    public function setCustomer($customer)
    {
        if ($this->customer != $customer) {
            $this->customer = $customer;
            $this->assignSiteUser($customer);
            return $this;
        }
    }

    public function getEmail()
    {
        return $this->customer->getEmail();
    }

    public function setEmail($email)
    {
        return $this->customer->setEmail($email);
    }

    public function getEmailCanonical()
    {
        return $this->customer->getEmailCanonical();
    }

    public function setEmailCanonical($emailCanonical)
    {
        return $this->customer->setEmailCanonical($emailCanonical);
    }

    /**
     * @param Customer $customer
     */
    protected function assignSiteUser($customer = null)
    {
        if (null !== $customer) {
            $customer->setSiteUser($this);
        }
    }
}