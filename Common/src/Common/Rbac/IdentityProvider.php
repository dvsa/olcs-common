<?php

/**
 * Identity Provider
 */
namespace Common\Rbac;

use Common\Service\Cqrs\Query\QuerySender;
use Dvsa\Olcs\Transfer\Query\MyAccount\MyAccount;
use Zend\Http\Header\GenericHeader;
use Zend\Http\Request;
use Zend\Session\Container;
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
     * @var Container
     */
    private $session;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param QuerySender $queryService
     */
    public function __construct(QuerySender $queryService, Container $session, Request $request)
    {
        $this->queryService = $queryService;
        $this->session = $session;
        $this->request = $request;
    }

    /**
     * Get the identity
     *
     * @return null|IdentityInterface
     * @throws \Exception
     */
    public function getIdentity()
    {
        if ($this->hasIdentityChanged()) {
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

            $this->session->offsetSet('identity', $user);
        }

        return $this->session->offsetGet('identity');
    }

    /**
     * Checks if identity we have in session still matches the request
     *
     * @return bool
     */
    private function hasIdentityChanged()
    {
        if (!$this->session->offsetExists('identity')) {
            // no identity in the session yet - refresh
            return true;
        }

        $identity = $this->session->offsetGet('identity');

        if (!($identity instanceof User)) {
            // no identity in the session yet - refresh
            return true;
        }

        $cookies = $this->request->getCookie();

        $pid = $this->request->getHeader('X-Pid', new GenericHeader())->getFieldValue();

        if (!empty($cookies['secureToken']) && !empty($pid)) {
            // user authenticated
            if ($identity->getPid() !== $pid) {
                // but the one in session has different pid - refresh
                return true;
            }
        } else {
            // user not authenticated
            if (!$identity->isAnonymous()) {
                // but the one in session is not anonymous - refresh
                return true;
            }
        }

        return false;
    }
}
