<?php

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blog
 * @ORM\Entity
 * @ORM\Table(name="donate")
 */
class Donate
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
     * @ORM\Column(name="etat", type="string")
     */
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="information", type="string", length=255, nullable=true)
     */
    private $information;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="IdDonator",referencedColumnName="id")
     *
     */
    private $donator;
    /**
     *
     * @ORM\Column(name="payment_id", type="string")
     */
    private $paymentId;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;
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
     * @ORM\Column(name="montant", type="integer")
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="text" , nullable=false)
     */
    protected $adresse = "";

    /**
     * @var string
     *
     * @ORM\Column(name="societe", type="string", length=100,nullable=true)
     */
    protected $societe;

    /**
     *
     * @ORM\Column(name="telephone", type="string", length=3000)
     */
    protected $telephone;

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
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param string $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    /**
     * @return string
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * @param string $information
     */
    public function setInformation($information)
    {
        $this->information = $information;
    }

    /**
     * @return mixed
     */
    public function getDonator()
    {
        return $this->donator;
    }

    /**
     * @param mixed $donator
     */
    public function setDonator($donator)
    {
        $this->donator = $donator;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @param \DateTime $dateCreation
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     * @return mixed
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * @param mixed $montant
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;
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
    public function setClientIp($clientIp)
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
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param string $adresse
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    /**
     * @return string
     */
    public function getSociete()
    {
        return $this->societe;
    }

    /**
     * @param string $societe
     */
    public function setSociete($societe)
    {
        $this->societe = $societe;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }


}
