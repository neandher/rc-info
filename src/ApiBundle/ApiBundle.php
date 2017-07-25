<?php

namespace ApiBundle;

use ApiBundle\Util\GlobalsHelper;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiBundle extends Bundle
{
    public function boot()
    {
        $scheme = $this->container->getParameter('router.request_context.scheme');
        $host = $this->container->getParameter('router.request_context.host');

        $url = $scheme . '://' . $host;

        GlobalsHelper::setDownloadUrl(
            $url . $this->container->getParameter('upload_downloads_file_relative_path')
        );
    }
}
