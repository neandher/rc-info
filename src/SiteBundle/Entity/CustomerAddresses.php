<?php

namespace SiteBundle\Entity;

use AdminBundle\Entity\Uf;
use AppBundle\Resource\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="SiteBundle\Repository\CustomerAddressesRepository")
 * @ORM\Table(name="customer_addresses")
 */
class CustomerAddresses
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $street;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $district;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $postcode;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mainAddress;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $complement;

    /**
     * @var Uf
     *
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Uf")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $uf;

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
     * Set street
     *
     * @param string $street
     *
     * @return CustomerAddresses
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set district
     *
     * @param string $district
     *
     * @return CustomerAddresses
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return CustomerAddresses
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     *
     * @return CustomerAddresses
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set mainAddress
     *
     * @param string $mainAddress
     *
     * @return CustomerAddresses
     */
    public function setMainAddress($mainAddress)
    {
        $this->mainAddress = $mainAddress;

        return $this;
    }

    /**
     * Get mainAddress
     *
     * @return string
     */
    public function getMainAddress()
    {
        return $this->mainAddress;
    }

    /**
     * Set complement
     *
     * @param string $complement
     *
     * @return CustomerAddresses
     */
    public function setComplement($complement)
    {
        $this->complement = $complement;

        return $this;
    }

    /**
     * Get complement
     *
     * @return string
     */
    public function getComplement()
    {
        return $this->complement;
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

    /**
     * @return Uf
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * @param Uf $uf
     * @return CustomerAddresses
     */
    public function setUf($uf)
    {
        $this->uf = $uf;
        return $this;
    }
}
