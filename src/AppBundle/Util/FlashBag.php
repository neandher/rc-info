<?php

namespace AppBundle\Util;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBag as BaseFlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;

class FlashBag
{

    /**
     * @var BaseFlashBag
     */
    private $flashBag;
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(BaseFlashBag $flashBag, Translator $translator = null)
    {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function newMessage($type, $message, array $params = [], Session $previousSession = null)
    {
        $message = $this->translator === null ? $message : $this->translator->trans($message, $params);

        if ($previousSession) {
            $previousSession->getFlashBag()->add($type, $message);
        } else {
            $this->flashBag->add($type, $message);
        }
    }
}