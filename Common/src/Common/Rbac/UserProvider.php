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
        // test (Rollout.sql) users
        [1, 'teal\'c', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['internal-limited-read-only']],
        [2, 'daniel', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['internal-read-only']],
        [3, 'sam', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['internal-case-worker']],
        [4, 'jack', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['internal-admin']],
        [20, 'operator-admin', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['operator-admin']],
        [21, 'operator-user', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['operator-user']],
        [7, 'operator-tm', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['operator-tm']],
        [8, 'operator-ebsr', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['operator-ebsr']],
        [22, 'partner-admin', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['partner-admin']],
        [23, 'partner-user', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['partner-user']],
        [24, 'local-authority-admin', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6',
            ['local-authority-admin']],
        [25, 'local-authority-user', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6',
            ['local-authority-user']],

        // ETL users
        [336, 'usr336', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['internal-admin']],
        [542, 'usr542', '$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6', ['operator-admin']],
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
     * @var \Common\Service\Entity\UserEntityService
     */
    protected $userEntityService;

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
     * @param User $user
     */
    protected function populateUserData(User $user)
    {
        $userDetails = $this->getUserEntityService()->getUserDetails($user->getId());
        $user->setUserData($userDetails);
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
        $user = (isset($this->userObjectsByUsername[$username]) ? $this->userObjectsByUsername[$username] : null);
        $this->populateUserData($user);

        return $user;
    }

    /**
     * @param $id
     * @return null
     */
    public function findById($id)
    {
        $user = (isset($this->userObjectsById[$id]) ? $this->userObjectsById[$id] : null);
        $this->populateUserData($user);

        return $user;
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

    /**
     * @return \Common\Service\Entity\UserEntityService
     */
    public function getUserEntityService()
    {
        return $this->userEntityService;
    }

    /**
     * @param \Common\Service\Entity\UserEntityService $userEntityService
     */
    public function setUserEntityService($userEntityService)
    {
        $this->userEntityService = $userEntityService;
    }
}
