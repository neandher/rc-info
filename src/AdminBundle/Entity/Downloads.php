<?php

namespace AdminBundle\Entity;

use AppBundle\Resource\Model\TimestampableTrait;
use AppBundle\Resource\Model\ToggleableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\DownloadsRepository")
 * @ORM\Table(name="downloads")
 * @Vich\Uploadable()
 */
class Downloads
{
    use TimestampableTrait, ToggleableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @Vich\UploadableField(mapping="downloads_image", fileNameProperty="imageName")
     *
     * @var File
     * @Assert\NotBlank(groups={"create"})
     * @Assert\File(
     *     groups={"create"},
     *     mimeTypes = {"image/png", "image/jpg", "image/jpeg"}
     * )
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string")
     */
    private $imageName;

    /**
     * @Vich\UploadableField(mapping="downloads_file", fileNameProperty="downloadName")
     *
     * @var File
     * @Assert\NotBlank(groups={"create"})
     * @Assert\File(groups={"create"})
     */
    private $downloadFile;

    /**
     * @ORM\Column(type="string")
     */
    private $downloadName;

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
     * @return Downloads
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
            $this->setUpdatedAt(new \DateTime());
        }

        return $this;
    }

    /**
     * Set imageName
     *
     * @param string $imageName
     *
     * @return Downloads
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @return File
     */
    public function getDownloadFile()
    {
        return $this->downloadFile;
    }

    /**
     * @param File $downloadFile
     * @return Downloads
     */
    public function setDownloadFile(File $downloadFile = null)
    {
        $this->downloadFile = $downloadFile;

        if ($downloadFile instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }

        return $this;
    }

    /**
     * Set downlodName
     *
     * @param string $downloadName
     *
     * @return Downloads
     */
    public function setDownloadName($downloadName)
    {
        $this->downloadName = $downloadName;

        return $this;
    }

    /**
     * Get downlodName
     *
     * @return string
     */
    public function getDownloadName()
    {
        return $this->downloadName;
    }

    /**
     * Get isEnabled
     *
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }
}
