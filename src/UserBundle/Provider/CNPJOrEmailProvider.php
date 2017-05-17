<?php

namespace UserBundle\Provider;

class CNPJOrEmailProvider extends AbstractUserProvider
{
    /**
     * {@inheritdoc}
     */
    protected function findUser($cnpjOrEmail)
    {
        if (filter_var($cnpjOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->userRepository->findOneByEmail($cnpjOrEmail);
        }

        return $this->userRepository->findOneByCNPJ($cnpjOrEmail);
    }
}