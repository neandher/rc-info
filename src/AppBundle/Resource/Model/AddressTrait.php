<?php

namespace AppBundle\Resource\Model;

use AdminBundle\Entity\Uf;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait AddressTrait
{
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
    private $zipCode;

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
     * Set street
     *
     * @param string $street
     *
     * @return $this;
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
     * @return $this;
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
     * @return $this;
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
     * @param string $zipCode
     *
     * @return $this;
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set mainAddress
     *
     * @param string $mainAddress
     *
     * @return $this;
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
     * @return $this;
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
     * @return Uf
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * @param Uf $uf
     * @return $this;
     */
    public function setUf($uf)
    {
        $this->uf = $uf;
        return $this;
    }
}