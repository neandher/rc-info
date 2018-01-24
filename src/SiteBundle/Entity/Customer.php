<?php

namespace SiteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="SiteBundle\Repository\CustomerRepository")
 * @UniqueEntity(fields={"email"}, message="user.email.already_exists")
 * @UniqueEntity(fields={"cnpj"})
 * @Vich\Uploadable()
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
     * @var int
     *
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     * @Assert\LessThan(value="31")
     */
    private $billPayDay;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank()
     * @Assert\LessThan(value="99999999.99")
     */
    private $billAmount;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Url()
     */
    private $url;

    /**
     * @Vich\UploadableField(mapping="customer_image", fileNameProperty="imageName")
     *
     * @var File
     * @Assert\File(
     *     mimeTypes = {"image/png", "image/jpg", "image/jpeg"}
     * )
     */
    private $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $imageName;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     * @Assert\Length(max="255")
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime(format="d-m-Y - H:i")
     */
    private $publishedAt;

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

    /**
     * @return int
     */
    public function getBillPayDay()
    {
        return $this->billPayDay;
    }

    /**
     * @param int $billPayDay
     * @return Customer
     */
    public function setBillPayDay($billPayDay)
    {
        $this->billPayDay = $billPayDay;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillAmount()
    {
        return $this->billAmount;
    }

    /**
     * @param string $billAmount
     * @return Customer
     */
    public function setBillAmount($billAmount)
    {
        $this->billAmount = $billAmount;
        return $this;
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param File $imageFile
     * @return $this
     */
    public function setImageFile(File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if ($imageFile instanceof UploadedFile) {
            $this->getSiteUser()->setUpdatedAt(new \DateTime());
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @param string $imageName
     * @return $this
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     * @return $this
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }
}

