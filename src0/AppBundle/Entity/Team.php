<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Team
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="poste", type="string", length=255)
     */
    private $poste;
    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=255)
     */
    private $facebook;
    /**
     * @var string
     *
     * @ORM\Column(name="instagram", type="string", length=255)
     */
    private $instagram;
    /**
     * @var string
     *
     * @ORM\Column(name="linkedin", type="string", length=255)
     */
    private $linkedin;
    /**
     * @var string
     *
     * @ORM\Column(name="specialite", type="string", length=255)
     */
    private $specialite;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName")
     *
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $imageName;
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
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
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom( $nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * @param string $poste
     */
    public function setPoste( $poste)
    {
        $this->poste = $poste;
    }

    /**
     * @return string
     */
    public function getSpecialite()
    {
        return $this->specialite;
    }

    /**
     * @param string $specialite
     */
    public function setSpecialite( $specialite)
    {
        $this->specialite = $specialite;
    }

    /**
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param string $facebook
     */
    public function setFacebook(string $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @return string
     */
    public function getInstagram()
    {
        return $this->instagram;
    }

    /**
     * @param string $instagram
     */
    public function setInstagram(string $instagram)
    {
        $this->instagram = $instagram;
    }

    /**
     * @return string
     */
    public function getLinkedin()
    {
        return $this->linkedin;
    }

    /**
     * @param string $linkedin
     */
    public function setLinkedin(string $linkedin)
    {
        $this->linkedin = $linkedin;
    }


}
