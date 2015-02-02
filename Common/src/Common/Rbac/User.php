<?php
namespace Common\Rbac;

use ZfcUser\Entity\User as ZfcUser;
use ZfcRbac\Identity\IdentityInterface;

class User extends ZfcUser implements IdentityInterface
{
    protected $roles;

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}