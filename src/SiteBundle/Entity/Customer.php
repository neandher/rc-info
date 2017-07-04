<?php

namespace SiteBundle\Entity;

use AppBundle\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="SiteBundle\Repository\CustomerRepository")
 * @UniqueEntity(fields={"email"}, message="user.email.already_exists")
 * @UniqueEntity(fields={"cnpj"})
 */
class Customer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $emailCanonical;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $cnpj;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var SiteUser
     *
     * @ORM\OneToOne(targetEntity="SiteBundle\Entity\SiteUser", mappedBy="customer", cascade={"all"})
     * @Assert\Valid()
     */
    protected $siteUser;

    /**
     * @var CustomerAddresses
     *
     * @ORM\OneToMany(targetEntity="SiteBundle\Entity\CustomerAddresses", mappedBy="customer", cascade={"persist", "remove"})
     * @Assert\Count(min="1", minMessage="admin.customer_addresses.count_min")
     * @Assert\Valid()
     */
    private $customerAddresses;

    /**
     * Customer constructor.
     */
    public function __construct()
    {
        $this->customerAddresses = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set emailCanonical
     *
     * @param string $emailCanonical
     *
     * @return Customer
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * Get emailCanonical
     *
     * @return string
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * @return string
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * @param string $cnpj
     * @return Customer
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    /**
     * Set firstName
     *
     * @param string $name
     *
     * @return Customer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return Customer
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @return SiteUser
     */
    public function getSiteUser()
    {
        return $this->siteUser;
    }

    /**
     * @param SiteUser $siteUser
     * @return Customer
     */
    public function setSiteUser($siteUser)
    {
        if ($this->siteUser !== $siteUser) {
            $this->siteUser = $siteUser;
            $this->assignCustomer($siteUser);
        }
        return $this;
    }

    /**
     * @param SiteUser|null $siteUser
     */
    protected function assignCustomer(SiteUser $siteUser = null)
    {
        if (null !== $siteUser) {
            $siteUser->setCustomer($this);
        }
    }

    /**
     * @return ArrayCollection|CustomerAddresses[]
     */
    public function getCustomerAddresses()
    {
        return $this->customerAddresses;
    }

    /**
     * @param CustomerAddresses $customerAddress
     */
    public function addCustomerAddress(CustomerAddresses $customerAddress)
    {
        $customerAddress->setCustomer($this);
        $this->customerAddresses->add($customerAddress);
    }

    /**
     * @param CustomerAddresses $customerAddress
     */
    public function removeCustomerAddress(CustomerAddresses $customerAddress)
    {
        if ($this->customerAddresses->contains($customerAddress)) {
            $this->customerAddresses->removeElement($customerAddress);
        }
    }

    /**
     * @return CustomerAddresses
     */
    public function getMainAddress()
    {
        $mainAddress = null;
        foreach ($this->customerAddresses as $customerAddress) {
            if ($customerAddress->getMainAddress() == true) {
                $mainAddress = $customerAddress;
                break;
            }
        }
        return $mainAddress;
    }

    /**
     * @Assert\Callback()
     * @param ExecutionContextInterface $context
     * @param $payload
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $hasMainAddress = false;
        foreach ($this->customerAddresses as $customerAddress) {
            if ($customerAddress->getMainAddress() == true) {
                $hasMainAddress = true;
            }
        }

        if (!$hasMainAddress) {
            if ($this->customerAddresses->count() == 1) {
                $this->customerAddresses->first()->setMainAddress(true);
            } else {
                $context->buildViolation('admin.customer_addresses.hasMainAddress')
                    ->atPath('custommerAddresses')
                    ->addViolation();
            }
        }
    }
}

