<?php

namespace Common\Rbac;

use Common\Service\Cqrs\Query\QuerySender;
use Dvsa\Olcs\Transfer\Query\MyAccount\MyAccount;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Request;
use Laminas\Session\Container;
use Olcs\Logging\Log\Logger;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Identity\IdentityProviderInterface;

/**
 * Pid Identity Provider
 */
class PidIdentityProvider implements IdentityProviderInterface
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
     * @var CacheEncryption
     */
    private $cacheService;

    /**
     * @var IdentityInterface;
     */
    private $identity;

    /**
     * IdentityProvider constructor.
     *
     * @param QuerySender     $queryService
     * @param Container       $session
     * @param Request         $request
     * @param CacheEncryption $cacheService
     *
     * @return void
     */
    public function __construct(
        QuerySender $queryService,
        Container $session,
        Request $request,
        CacheEncryption $cacheService
    ) {
        $this->queryService = $queryService;
        $this->session = $session;
        $this->request = $request;
        $this->cacheService = $cacheService;
    }

    /**
     * Get the identity
     *
     * It would be easier not to, but we use a custom cache implementation here specific to the identity provider.
     * Since we have no permission system for the Redis caches in the way we do for the backend API, this allows us
     * to protect the user information by validating the user based on the session contents. We refresh the user data
     * here in order to prevent the user data in the session (which can't be easily expired) from becoming out of sync
     * with what's in the caches
     *
     * @return null|IdentityInterface
     * @throws \Exception
     */
    public function getIdentity()
    {
        if ($this->identity === null) {
            /** @var User|null $identity */
            $identity = $this->session->offsetGet('identity');

            //the session we have may be fine, but we may wish to update the user data
            if (!$this->shouldUpdateIdentity($identity)) {
                $userId = $identity->getId();

                //retrieve user id and check the cache exists for that user, if so update the session data
                if ($this->cacheService->hasCustomItem(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $userId)) {
                    Logger::debug('try fetching user info from cache: ' . $userId);
                    $data = $this->cacheService->getCustomItem(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $userId);
                    $identity->setUserType($data['userType']);
                    $identity->setUsername($data['loginId']);
                    $identity->setUserData($data);

                    $this->session->offsetSet('identity', $identity);
                    $this->identity = $identity;

                    return $this->identity;
                }
            }

            //nothing in the session, or cache doesn't exist, we need to retrieve user details from the backend
            Logger::debug('fetching user info from database');
            $this->queryService->setRecoverHttpClientException(true);
            $response = $this->queryService->send(MyAccount::create([]));

            if (!$response->isOk()) {
                    $response->setResult(
                        [
                            'id' => null,
                            'pid' => null,
                            'userType' => User::USER_TYPE_NOT_IDENTIFIED,
                            'loginId' => null,
                            'roles' => []
                        ]
                    );
            }

            $data = $response->getResult();

            $user = new User();
            if (!empty($data['id'])) {
                $user->setId($data['id']);
            }
            $user->setPid($data['pid']);
            $user->setUserType($data['userType']);
            $user->setUsername($data['loginId']);
            $user->setUserData($data);

            if (!empty($data['roles']) && is_array($data['roles'])) {
                $roles = [];
                foreach ($data['roles'] as $role) {
                    $roles[] = $role['role'];
                }
                $user->setRoles($roles);
            }

            $this->identity = $user;
            $this->session->offsetSet('identity', $this->identity);
        }

        return $this->identity;
    }

    /**
     * Checks if identity we have in session still matches the request
     *
     * @param User|null
     *
     * @return bool
     */
    private function shouldUpdateIdentity(?User $identity): bool
    {
        if (!($identity instanceof User)) {
            // no identity in the session yet - refresh
            return true;
        }

        if (empty($identity->getId())) {
            //no user id - refresh
            return true;
        }

        $cookie = $this->request->getCookie();

        $pid = $this->request->getHeader('X-Pid', new GenericHeader())->getFieldValue();

        if (!empty($cookie['secureToken']) && !empty($pid)) {
            // user authenticated
            if ($identity->getPid() !== $pid) {
                // but the one in session has different pid - refresh
                return true;
            }
        } else {
            // user not authenticated
            return true;
        }

        return false;
    }

    public function clearSession(): void
    {
        $this->session->exchangeArray([]);
        $this->identity = null;
    }
}
