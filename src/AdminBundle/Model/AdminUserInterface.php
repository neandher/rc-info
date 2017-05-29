<?php

namespace AdminBundle\Model;

use UserBundle\Model\UserInterface as BaseUserInterface;

interface AdminUserInterface extends BaseUserInterface
{
    const DEFAULT_ADMIN_ROLE = 'ROLE_ADMINISTRATION_ACCESS';
    const SUPER_ADMIN_ROLE = 'ROLE_SUPER_ADMIN';

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

    /**
     * Tells if the the given user has the super admin role.
     *
     * @return bool
     */
    public function isSuperAdmin();

    /**
     * Sets the super admin status.
     *
     * @param bool $boolean
     *
     * @return self
     */
    public function setSuperAdmin($boolean);
}