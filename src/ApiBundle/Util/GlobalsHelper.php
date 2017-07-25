<?php

namespace ApiBundle\Util;

class GlobalsHelper
{
    /**
     * @var string
     */
    protected static $downloadUrl;

    /**
     * @return string
     */
    public static function getDownloadUrl()
    {
        return self::$downloadUrl;
    }

    /**
     * @param string $downloadUrl
     */
    public static function setDownloadUrl($downloadUrl)
    {
        self::$downloadUrl = $downloadUrl;
    }
}