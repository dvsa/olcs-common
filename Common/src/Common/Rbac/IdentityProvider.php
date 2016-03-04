<?php

/**
 * Identity Provider
 */
namespace Common\Rbac;

use Common\Service\Cqrs\Query\QuerySender;
use Dvsa\Olcs\Transfer\Query\MyAccount\MyAccount;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Identity\IdentityProviderInterface;

/**
 * Identity Provider
 */
class IdentityProvider implements IdentityProviderInterface
{
    /**
     * @var QuerySender
     */
    private $queryService;

    /**
     * @var Identity;
     */
    private $identity;

    /**
     * @param QuerySender $queryService
     */
    public function __construct(QuerySender $queryService)
    {
        $this->queryService = $queryService;
    }

    /**
     * Get the identity
     *
     * @return null|IdentityInterface
     * @throws \Exception
     */
    public function getIdentity()
    {
        if ($this->identity === null) {
            $response = $this->queryService->send(MyAccount::create([]));

            if (!$response->isOk()) {
                throw new \Exception('Unable to retrieve identity');
            }

            $data = $response->getResult();

            $user = new User();
            $user->setId($data['id']);
            $user->setPid($data['pid']);
            $user->setUserType($data['userType']);
            $user->setUsername($data['loginId']);
            $user->setUserData($data);

            $roles = [];
            foreach ($data['roles'] as $role) {
                $roles[] = $role['role'];
            }
            $user->setRoles($roles);

            $this->identity = $user;
        }

        return $this->identity;
    }
}
