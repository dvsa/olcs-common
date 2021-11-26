<?php
declare(strict_types=1);

namespace Common\Rbac;

use Common\Auth\Service\RefreshTokenService;
use Common\Service\Cqrs\Query\QuerySender;
use Dvsa\Olcs\Transfer\Query\MyAccount\MyAccount;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Exception;
use Laminas\Session\Container;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Identity\IdentityProviderInterface;

/**
 * @see JWTIdentityProviderFactory
 */
class JWTIdentityProvider implements IdentityProviderInterface
{
    private ?IdentityInterface $identity = null;
    private Container $session;
    private QuerySender $querySender;
    private CacheEncryption $cacheService;
    private RefreshTokenService $refreshTokenService;

    public function __construct(
        Container $session,
        QuerySender $querySender,
        CacheEncryption $cacheService,
        RefreshTokenService $refreshTokenService
    ) {
        $this->session = $session;
        $this->querySender = $querySender;
        $this->cacheService = $cacheService;
        $this->refreshTokenService = $refreshTokenService;
    }

    public function getIdentity()
    {
        if (!is_null($this->identity)) {
            return $this->identity;
        }

        $identity = $this->session->offsetGet('identity');

        if (!$this->shouldUpdateIdentity($identity) && $this->cacheHasIdentity($identity->getId())) {
            $data = $this->fetchIdentityFromCache($identity->getId());
            $this->refreshTokenIfRequired($data['loginId']);
        } else {
            if ($identity instanceof User && !is_null($identity->getUsername())) {
                // Refresh the token if required before we try to make the call to DB
                $this->refreshTokenIfRequired($identity->getUsername());
            }

            $data = $this->fetchIdentityFromDB();
            $identity = new User();
            $identity->setId($data['id']);

            if (!empty($data['roles']) && is_array($data['roles'])) {
                $identity->setRoles(array_column($data['roles'], 'role'));
            }
        }

        $identity->setUserType($data['userType']);
        $identity->setUsername($data['loginId']);
        $identity->setUserData($data);

        $this->identity = $identity;
        $this->session->offsetSet('identity', $this->identity);
        return $this->identity;
    }

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

        return false;
    }

    /**
     * @return array|mixed
     */
    private function fetchIdentityFromDB()
    {
        $this->querySender->setRecoverHttpClientException(true);
        $response = $this->querySender->send(MyAccount::create([]));

        if (!$response->isOk()) {
            $response->setResult(
                [
                    'id' => null,
                    'userType' => User::USER_TYPE_NOT_IDENTIFIED,
                    'loginId' => null,
                    'roles' => []
                ]
            );
        }

        return $response->getResult();
    }

    private function fetchIdentityFromCache(int $userId): array
    {
        return $this->cacheService->getCustomItem(CacheEncryption::USER_ACCOUNT_IDENTIFIER, (string)$userId);
    }

    private function cacheHasIdentity(int $userId): bool
    {
        return $this->cacheService->hasCustomItem(CacheEncryption::USER_ACCOUNT_IDENTIFIER, (string)$userId);
    }

    private function refreshTokenIfRequired(string $identifier): void
    {
        $token = $this->session->offsetGet('storage')['Token'] ?? null;
        if (is_null($token) || !$this->refreshTokenService->isRefreshRequired($token)) {
            return;
        }

        try {
            $newToken = $this->refreshTokenService->refreshToken($token, $identifier);
            $this->session->offsetSet('storage', $newToken);
        } catch (Exception $e) {
            return;
        }
    }
}
