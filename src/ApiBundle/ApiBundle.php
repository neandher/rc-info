<?php

namespace ApiBundle;

use ApiBundle\Util\GlobalsHelper;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiBundle extends Bundle
{
    public function boot()
    {
        $host = $this->container->getParameter('router.request_context.host');
        $scheme = $this->container->getParameter('router.request_context.scheme');

        $url = $scheme . '://' . $host;
        
        GlobalsHelper::setDownloadUrl(
            $url . '/' . $this->container->getParameter('upload_downloads_file_relative_path')
        );
    }
}
