<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="fos_user")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class User extends BaseUser implements \Hackzilla\Bundle\TicketBundle\Model\UserInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     *
     * @ORM\Column(name="telephone", type="integer", length=3000)
     */
    protected $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100)
     */

    protected $nom;
    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=100)
     */
    protected $prenom;
    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="text" , nullable=false)
     */
    protected $adresse = "";
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName", size="imageSize")
     *
     * @var File|null
     */
    protected $imageFile;
    /**
     * @var string
     *
     * @ORM\Column(name="societe", type="string", length=100,nullable=true)
     */
    protected $societe;
    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    protected $imageName;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /** @ORM\Column(name="google_id", type="string", length=255, nullable=true) */
    protected $google_id;

    /** @ORM\Column(name="google_access_token", type="string", length=255, nullable=true) */
    protected $google_access_token;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
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
     * @param string $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }


    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @ORM\PrePersist
     */
    public function setImageName($image_name)
    {
        $this->imageName = $image_name;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
     * @throws \Exception
     */
    public function setImageFile(?File $imageFile = null)
    {
        $this->imageFile = $imageFile;

    }

    public function getImageFile()
    {
        return $this->imageFile;
    }


    function getTelephone()
    {
        return $this->telephone;
    }

    function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * @param mixed $facebook_id
     */
    public function setFacebookId($facebook_id)
    {
        $this->facebook_id = $facebook_id;
    }

    /**
     * @return mixed
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * @param mixed $facebook_access_token
     */
    public function setFacebookAccessToken($facebook_access_token)
    {
        $this->facebook_access_token = $facebook_access_token;
    }

    /**
     * @return mixed
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * @param mixed $google_id
     */
    public function setGoogleId($google_id)
    {
        $this->google_id = $google_id;
    }

    /**
     * @return mixed
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * @param mixed $google_access_token
     */
    public function setGoogleAccessToken($google_access_token)
    {
        $this->google_access_token = $google_access_token;
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


}