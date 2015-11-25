<?php

namespace Common\Rbac;

use Common\Service\Cqrs\Query\QuerySender;
use Common\Service\Cqrs\Query\QuerySenderAwareInterface;
use Common\Service\Cqrs\Query\QuerySenderAwareTrait;
use Common\Service\Cqrs\Response;
use Dvsa\Olcs\Transfer\Query\User\ZfcUser as Query;
use Zend\Session\Container;
use ZfcUser\Mapper\UserInterface;

/**
 * User Provider
 *
 * @todo Remove this class when we are fully integrated with OpenAM
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UserProvider implements UserInterface, QuerySenderAwareInterface
{
    use QuerySenderAwareTrait;

    private $session;

    public function __construct(QuerySender $querySender, Container $session)
    {
        $this->setQuerySender($querySender);

        $this->session = $session;
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
        $query = Query::create(['username' => $username]);

        $response = $this->getQuerySender()->send($query);

        return $this->getUserFromResponse($response);
    }

    /**
     * @param $id
     * @return null
     */
    public function findById($id)
    {
        $query = Query::create(['id' => $id]);

        $response = $this->getQuerySender()->send($query);

        return $this->getUserFromResponse($response);
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

    protected function getUserFromResponse(Response $response)
    {
        if ($response->isOk() === false) {
            return null;
        }

        $result = $response->getResult();

        $user = new ZfcUser();
        $user->setId($result['id']);
        $user->setPid($result['pid']);
        $user->setUserType($result['userType']);
        $user->setUsername($result['loginId']);
        $user->setUserData($result);
        // Hard-code password as we don't want to fetch it
        $user->setPassword('$2a$12$I/kfi/F3uRflYV5Nnk48vuTf1zFkbhctnk0xmVnZHKQupPZc6mtk6');

        $roles = [];
        foreach ($result['roles'] as $role) {
            $roles[] = $role['role'];
        }
        $user->setRoles($roles);

        $this->session->offsetSet('identity', $user);

        return $user;
    }
}
