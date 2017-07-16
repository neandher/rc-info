<?php

namespace AdminBundle\Entity;

use SiteBundle\Annotation\CustomerAware;
use AppBundle\Resource\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use SiteBundle\Entity\Customer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\BillRepository")
 * @ORM\Table(name="bill")
 * @CustomerAware(customerFieldName="customer_id")
 */
class Bill
{
    use TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @var \Datetime
     *
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $dueDateAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Date()
     */
    private $paymentDateAt;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank()
     * @Assert\LessThan(value="99999999.99")
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     * @Assert\LessThan(value="999999999.99")
     */
    private $amountPaid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $note;

    /**
     * @var BillStatus
     *
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\BillStatus")
     */
    private $billStatus;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="SiteBundle\Entity\Customer")
     * @Assert\NotNull()
     */
    private $customer;

    /**
     * @var BillRemessa
     *
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\BillRemessa", inversedBy="bill", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $billRemessa;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Bill
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDueDateAt()
    {
        return $this->dueDateAt;
    }

    /**
     * @param \DateTime $dueDateAt
     * @return Bill
     */
    public function setDueDateAt($dueDateAt)
    {
        $this->dueDateAt = $dueDateAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPaymentDateAt()
    {
        return $this->paymentDateAt;
    }

    /**
     * @param \DateTime $paymentDateAt
     * @return Bill
     */
    public function setPaymentDateAt($paymentDateAt)
    {
        $this->paymentDateAt = $paymentDateAt;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return Bill
     */
    public function setAmount($amount)
    {
        //$this->amount = (float)str_replace(['-', '.', ','], ['', '', '.'], $amount);
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    /**
     * @param float $amountPaid
     * @return Bill
     */
    public function setAmountPaid($amountPaid)
    {
        //$this->amountPaid = (float)str_replace(['-', '.', ','], ['', '', '.'], $amountPaid);
        $this->amountPaid = $amountPaid;
        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     * @return Bill
     */
    public function setNote($note)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return BillStatus
     */
    public function getBillStatus()
    {
        return $this->billStatus;
    }

    /**
     * @param BillStatus $billStatus
     * @return Bill
     */
    public function setBillStatus($billStatus)
    {
        $this->billStatus = $billStatus;
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
     * @param Customer $customer
     * @return Bill
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDateOverDue()
    {
        $isDateOverDue = false;

        if ($this->getPaymentDateAt() === null && $this->getDueDateAt() < (new \DateTime(date('y-m-d')))) {
            $isDateOverDue = true;
        }

        return $isDateOverDue;
    }

    /**
     * @return BillRemessa
     */
    public function getBillRemessa()
    {
        return $this->billRemessa;
    }

    /**
     * @param BillRemessa $billRemessa
     * @return Bill
     */
    public function setBillRemessa($billRemessa)
    {
        $this->billRemessa = $billRemessa;
        return $this;
    }
}