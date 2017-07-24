<?php

namespace AdminBundle\Entity;

use AppBundle\Resource\Model\TimestampableTrait;
use AppBundle\Resource\Model\ToggleableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\DownloadsRepository")
 * @ORM\Table(name="downloads")
 * @Vich\Uploadable()
 * @Serializer\ExclusionPolicy("all")
 */
class Downloads
{
    use TimestampableTrait, ToggleableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Serializer\Expose()
     */
    private $description;

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
     * @Serializer\Expose()
     */
    private $downloadName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime(format="d-m-Y - H:i")
     * @Serializer\Expose()
     */
    private $publishedAt;

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

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     * @return Downloads
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }
}
