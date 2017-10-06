<?php

namespace AdminBundle\Entity;

use ApiBundle\Util\GlobalsHelper;
use AppBundle\Resource\Model\TimestampableTrait;
use AppBundle\Resource\Model\ToggleableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\VideoRepository")
 * @ORM\Table(name="video")
 */
class Video
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
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime(format="d-m-Y - H:i")
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
     * @return Video
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
     * Set url
     *
     * @param string $url
     *
     * @return Video
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return Video
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
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
     *
     * @return Video
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdUrlYoutube()
    {
        $parts = parse_url($this->url);

        if ( ! empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        return ! empty($query['v']) ? $query['v'] : '';
    }

    /**
     * @param string $res [optional] <p>
     * Default: default
     * High: hqdefault
     * Medium: mqdefault
     * Standard: sddefault
     * Maximum: maxresdefault
     * </p>
     *
     * @return string
     */
    public function getThumbYoutube($res = 'default')
    {
        return 'https://img.youtube.com/vi/'.$this->getIdUrlYoutube().'/'.$res.'.jpg';
    }
}