<?php

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Event
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
     * @ORM\Column(name="nomEvenement", type="string", length=255, nullable=true , unique=true)
     */
    private $nomEvenement;

    /**
     * @var string
     *
     * @ORM\Column(name="adr", type="string", length=255)
     */
    private $adr;
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    /**
     * @var boolean
     *
     * @ORM\Column(name="special", type="boolean")
     */
    private $special;
    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float",nullable=true)
     */
    private $price;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetime")
     */
    private $start;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetime")
     */
    private $end;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName", size="imageSize")
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
     * @ORM\Column(type="boolean")
     */
    private $marathon;

    /**
     * @return mixed
     */
    public function getMarathon()
    {
        return $this->marathon;
    }

    /**
     * @param mixed $marathon
     */
    public function setMarathon($marathon): void
    {
        $this->marathon = $marathon;
    }


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName)
    {
        $this->imageName = $imageName;
    }

    public function getImageName()
    {
        return $this->imageName;
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNomEvenement()
    {
        return $this->nomEvenement;
    }

    /**
     * @param string $nomEvenement
     */
    public function setNomEvenement(string $nomEvenement)
    {
        $this->nomEvenement = $nomEvenement;
    }

    /**
     * @return string
     */
    public function getAdr()
    {
        return $this->adr;
    }

    /**
     * @param string $adr
     */
    public function setAdr(string $adr)
    {
        $this->adr = $adr;
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
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start)
    {
        $this->start = $start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end)
    {
        $this->end = $end;
    }

    /**
     * @return bool
     */
    public function getSpecial()
    {
        return $this->special;
    }

    /**
     * @param bool $special
     */
    public function setSpecial(bool $special)
    {
        $this->special = $special;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

}
