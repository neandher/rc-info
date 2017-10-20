<?php

namespace AppBundle\Resource\Model;

use JMS\Serializer\Annotation as Serializer;

trait ToggleableTrait
{
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * @Serializer\Expose()
     */
    protected $isEnabled = true;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
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
     * @param bool $isEnabled
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = (bool) $isEnabled;
    }

    public function enable()
    {
        $this->isEnabled = true;
    }

    public function disable()
    {
        $this->isEnabled = false;
    }
}