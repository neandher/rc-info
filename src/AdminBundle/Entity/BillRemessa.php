<?php

namespace AdminBundle\Entity;

use AppBundle\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\BillRemessaRepository")
 * @ORM\Table(name="bill_remessa")
 */
class BillRemessa
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
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sent;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"sent"})
     */
    private $sentAt;

    /**
     * @var Bill
     *
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\Bill", mappedBy="billRemessa", cascade={"remove"})
     */
    private $bill;

    /**
     * BillRemessa constructor.
     */
    public function __construct()
    {
        $this->bill = new ArrayCollection();
    }


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
     * Set description
     *
     * @param string $description
     *
     * @return BillRemessa
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set sent
     *
     * @param boolean $sent
     *
     * @return BillRemessa
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return boolean
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * @param \DateTime $sentAt
     * @return BillRemessa
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    /**
     * @return ArrayCollection|Bill
     */
    public function getBills()
    {
        return $this->bill;
    }

    /**
     * @param Bill $bill
     */
    public function addBill(Bill $bill)
    {
        $bill->setBillRemessa($this);
        $this->bill->add($bill);
    }

    /**
     * @param Bill $bill
     */
    public function removeBill(Bill $bill)
    {
        if ($this->bill->contains($bill)) {
            $this->bill->removeElement($bill);
        }
    }
}
