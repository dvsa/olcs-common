<?php
namespace Common\Rbac;

use ZfcUser\Entity\User as ZfcUser;
use ZfcRbac\Identity\IdentityInterface;

/**
 * Class User
 * @package Common\Rbac
 */
class User extends ZfcUser implements IdentityInterface
{
    /**
     * @var array
     */
    protected $roles = [];

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}
