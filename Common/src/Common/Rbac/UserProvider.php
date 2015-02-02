<?php

namespace Common\Rbac;

use ZfcUser\Mapper\UserInterface;

class UserProvider implements UserInterface
{
    protected $users = [
        [1, 'tom', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['limited-read-only']]
    ];

    protected $userObjectsByUsername = [];
    protected $userObjectsById = [];

    public function __construct()
    {
        foreach ($this->users as $user) {
            $userObject = new User();
            $userObject->setRoles(array_pop($user));
            $userObject->setPassword(array_pop($user));
            $userObject->setUsername(array_pop($user));
            $userObject->setId(array_pop($user));

            $this->userObjectsById[$userObject->getId()] = $userObject;
            $this->userObjectsByUsername[$userObject->getUsername()] = $userObject;
        }
    }

    public function findByEmail($email)
    {
        return null;
    }

    public function findByUsername($username)
    {
        return (isset($this->userObjectsByUsername[$username]) ? $this->userObjectsByUsername[$username] : null);
    }

    public function findById($id)
    {
        return (isset($this->userObjectsById[$id]) ? $this->userObjectsById[$id] : null);
    }

    public function insert($user)
    {

    }

    public function update($user)
    {

    }
}