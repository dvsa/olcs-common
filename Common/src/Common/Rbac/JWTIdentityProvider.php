<?php
declare(strict_types=1);

namespace Common\Rbac;

use Common\Auth\Service\RefreshTokenService;
use Common\Service\Cqrs\Query\QuerySender;
use Dvsa\Olcs\Transfer\Query\MyAccount\MyAccount;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Exception;
use Laminas\Authentication\Storage\Session;
use Laminas\Session\Container;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Identity\IdentityProviderInterface;

/**
 * @see JWTIdentityProviderFactory
 */
class JWTIdentityProvider implements IdentityProviderInterface
{
    private ?IdentityInterface $identity = null;
    private Container $identitySession;
    private QuerySender $querySender;
    private CacheEncryption $cacheService;
    private RefreshTokenService $refreshTokenService;
    private Session $tokenSession;

    public function __construct(
        Container $session,
        QuerySender $querySender,
        CacheEncryption $cacheService,
        RefreshTokenService $refreshTokenService,
        Session $tokenSession
    ) {
        $this->identitySession = $session;
        $this->querySender = $querySender;
        $this->cacheService = $cacheService;
        $this->refreshTokenService = $refreshTokenService;
        $this->tokenSession = $tokenSession;
    }

    public function getIdentity()
    {
        $this->refreshTokenIfRequired();

        if (!is_null($this->identity)) {
            return $this->identity;
        }

        $identity = $this->identitySession->offsetGet('identity');

        if (!$this->shouldUpdateIdentity($identity) && $this->cacheHasIdentity($identity->getId())) {
            $data = $this->fetchIdentityFromCache($identity->getId());
            $this->refreshTokenIfRequired();
        } else {
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
        $this->identitySession->offsetSet('identity', $this->identity);
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

    private function refreshTokenIfRequired(): void
    {
        if ($this->tokenSession->isEmpty()) {
            return;
        }

        $tokens = $this->tokenSession->read()['Token'] ?? null;
        $identifier = $this->tokenSession->read()['AccessTokenClaims']['username'] ?? null;
        if (is_null($tokens) || !$this->refreshTokenService->isRefreshRequired($tokens) || is_null($identifier)) {
            return;
        }

        try {
            $newTokens = $this->refreshTokenService->refreshTokens($tokens, $identifier);
            $this->tokenSession->write($newTokens);
        } catch (Exception $e) {
            return;
        }
    }

    public function clearSession(): void
    {
        $this->identitySession->exchangeArray([]);
        $this->tokenSession->clear();
        $this->identity = null;
    }
}
