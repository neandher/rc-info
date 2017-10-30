<?php

namespace SiteBundle\Twig;

class FileGetContents extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('file_get_contents', [
                $this,
                'fileGetContentsFunction'
            ]),
        ];
    }

    public function fileGetContentsFunction($file)
    {
        return file_get_contents($file);
    }
}