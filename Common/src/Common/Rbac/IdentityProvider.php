<?php

namespace Common\Rbac;

use Common\Service\Cqrs\Query\QueryService;
use ZfcRbac\Identity\IdentityInterface;
use ZfcRbac\Identity\IdentityProviderInterface;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;

class IdentityProvider implements IdentityProviderInterface
{
    /**
     * @var QueryService
     */
    private $queryService;

    /**
     * @var TransferAnnotationBuilder
     */
    private $annotationBuilder;

    /**
     * @var null
     */
    private $anonIdentity = false;

    /**
     * @var null
     */
    private $identity = null;

    /**
     * @param TransferAnnotationBuilder $annotationBuilder
     * @param QueryService $queryService
     */
    public function __construct(TransferAnnotationBuilder $annotationBuilder, QueryService $queryService)
    {
        $this->queryService = $queryService;
        $this->annotationBuilder = $annotationBuilder;
    }

    /**
     * Get the identity
     *
     * @TODO map more of the incoming data...
     * @return null|IdentityInterface
     * @throws \Exception
     */
    public function getIdentity()
    {
        if ($this->identity === null) {
            $query = $this->annotationBuilder->createQuery(new \Dvsa\Olcs\Transfer\Query\MyAccount\MyAccount());
            $response = $this->queryService->send($query);

            if ($response->isNotFound()) {
                $this->identity = $this->anonIdentity;
                return $this->anonIdentity;
            }

            if (!$response->isOk()) {
                \Doctrine\Common\Util\Debug::dump($response->getBody());
                throw new \Exception('Unable to retrieve identity');
            }

            $data = $response->getResult();
            $roles = [];

            $user = new User();
            $user->setUserData($data);

            foreach ($data['roles'] as $role) {
                $roles[] = $role['role'];
            }

            $user->setRoles($roles);

            $this->identity = $user;
        }

        return $this->identity;
    }
}