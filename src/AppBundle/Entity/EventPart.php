<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participants
 * @ORM\Entity
 * @ORM\Table(name="EventPart")
 */
class EventPart
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="userId",referencedColumnName="id",onDelete="CASCADE")
     *
     */
    private $userid;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event")
     * @ORM\JoinColumn(name="event_id",referencedColumnName="id" ,onDelete="CASCADE")
     *
     */
    private $evenement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateInscrit;
    /**
     * Client IP is registered only one time when the order(donation) is created
     *
     * @var string|null
     *
     * @ORM\Column(length=50, nullable=true)
     */
    private $clientIp;
    /**
     *
     * @ORM\Column(name="payment_id", type="string")
     */
    private $paymentId;
    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string")
     */
    private $etat;
    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="integer")
     */
    private $montant;

    /**
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * @param string $montant
     */
    public function setMontant(string $montant)
    {
        $this->montant = $montant;
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
     * @return mixed
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param mixed $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @return mixed
     */
    public function getEvenement()
    {
        return $this->evenement;
    }

    /**
     * @param mixed $evenement
     */
    public function setEvenement($evenement)
    {
        $this->evenement = $evenement;
    }

    /**
     * @return \DateTime
     */
    public function getDateInscrit()
    {
        return $this->dateInscrit;
    }

    /**
     * @param \DateTime $dateInscrit
     */
    public function setDateInscrit($dateInscrit)
    {
        $this->dateInscrit = $dateInscrit;
    }

    /**
     * @return string|null
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * @param string|null $clientIp
     */
    public function setClientIp( $clientIp)
    {
        $this->clientIp = $clientIp;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param mixed $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param string $etat
     */
    public function setEtat( $etat)
    {
        $this->etat = $etat;
    }


}