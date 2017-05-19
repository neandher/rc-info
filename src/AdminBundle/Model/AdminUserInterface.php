<?php

namespace AdminBundle\Model;

use UserBundle\Model\UserInterface as BaseUserInterface;

interface AdminUserInterface extends BaseUserInterface
{
    const DEFAULT_ADMIN_ROLE = 'ROLE_ADMINISTRATION_ACCESS';

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $lastName
     */
    public function setLastName($lastName);
}