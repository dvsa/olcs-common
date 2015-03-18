<?php

namespace Common\Rbac;

use ZfcUser\Mapper\UserInterface;

/**
 * Class UserProvider
 * @package Common\Rbac
 */
class UserProvider implements UserInterface
{
    /**
     * @var array
     */
    protected $users = [
        [1, 'teal\'c', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['limited-read-only']],
        [2, 'daniel', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['read-only']],
        [3, 'sam', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['case-worker']],
        [4, 'jack', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['admin']],
        [5, 'operator-admin', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['operator-admin']],
        [6, 'operator-user', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['operator-user']],
        [7, 'operator-tm', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['operator-tm']],
        [8, 'operator-ebsr', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['operator-ebsr']],
        [9, 'partner-admin', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['partner-admin']],
        [10, 'partner-user', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['partner-user']],
        [11, 'local-authority-admin', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6',
            ['local-authority-admin']],
        [12, 'local-authority-user', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6',
            ['local-authority-user']],
    ];

    /**
     * @var array
     */
    protected $userObjectsByUsername = [];
    /**
     * @var array
     */
    protected $userObjectsById = [];

    /**
     *
     */
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

    /**
     * @param $email
     * @return null
     */
    public function findByEmail($email)
    {
        return null;
    }

    /**
     * @param $username
     * @return null
     */
    public function findByUsername($username)
    {
        return (isset($this->userObjectsByUsername[$username]) ? $this->userObjectsByUsername[$username] : null);
    }

    /**
     * @param $id
     * @return null
     */
    public function findById($id)
    {
        return (isset($this->userObjectsById[$id]) ? $this->userObjectsById[$id] : null);
    }

    /**
     * @param $user
     */
    public function insert($user)
    {

    }

    /**
     * @param $user
     */
    public function update($user)
    {

    }
}
