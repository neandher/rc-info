<?php

namespace AdminBundle\Downloads;

use AdminBundle\Entity\Downloads;
use Vich\UploaderBundle\Handler\DownloadHandler;

class DownloadFile
{
    /**
     * @var DownloadHandler
     */
    private $handler;

    /**
     * DownloadFile constructor.
     * @param DownloadHandler $handler
     */
    public function __construct(DownloadHandler $handler)
    {
        $this->handler = $handler;
    }

    public function download(Downloads $downloads)
    {
        return $this->handler->downloadObject($downloads, $fileField = 'downloadFile');
    }
}