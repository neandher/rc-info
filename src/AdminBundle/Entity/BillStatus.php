<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BillStatus
 *
 * @ORM\Table(name="bill_status")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\BillStatusRepository")
 */
class BillStatus
{
    const BILL_STATUS_EM_ABERTO = 'em_aberto';
    const BILL_STATUS_PAGO = 'pago';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $referency;


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
     * Set description
     *
     * @param string $description
     *
     * @return BillStatus
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
     * Set referency
     *
     * @param string $referency
     *
     * @return BillStatus
     */
    public function setReferency($referency)
    {
        $this->referency = $referency;

        return $this;
    }

    /**
     * Get referency
     *
     * @return string
     */
    public function getReferency()
    {
        return $this->referency;
    }
}

