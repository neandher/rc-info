<?php

namespace SiteBundle\Entity;

use AppBundle\Resource\Model\AddressTrait;
use AppBundle\Resource\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="SiteBundle\Repository\CustomerAddressesRepository")
 * @ORM\Table(name="customer_addresses")
 */
class CustomerAddresses
{
    use AddressTrait, TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="SiteBundle\Entity\Customer", inversedBy="customerAddresses")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $customer;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Customer $customer
     * @return CustomerAddresses
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
