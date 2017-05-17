<?php

namespace UserBundle\Security;

use Psr\Log\LoggerInterface;

class TokenGenerator
{

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    private $useOpenSsl;

    /**
     * TokenGeneratorHelper constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        if (defined('PHP_WINDOWS_VERSION_BUILD') && version_compare(PHP_VERSION, '5.3.4', '<')) {
            $this->useOpenSsl = false;
        } elseif (!function_exists('openssl_random_pseudo_bytes')) {
            if (null !== $this->logger) {
                $this->logger->notice(
                    'It is recommended that you enable the "openssl" extension for random number generation.'
                );
            }
            $this->useOpenSsl = false;
        } else {
            $this->useOpenSsl = true;
        }
    }

    public function generateToken()
    {
        return rtrim(strtr(base64_encode($this->getRandomNumber()), '+/', '-_'), '=');
    }

    private function getRandomNumber()
    {
        $nbBytes = 32;

        if ($this->useOpenSsl) {
            $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);

            if (false !== $bytes && true === $strong) {
                return $bytes;
            }

            if (null !== $this->logger) {
                $this->logger->info('OpenSSL did not produce a secure random number.');
            }
        }

        return hash('sha256', uniqid(mt_rand(), true), true);
    }
}