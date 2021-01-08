<?php

namespace Common\Service\Data;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserTypesListDataService
 * Provides list options for user types
 *
 * @package Olcs\Service\Data
 */
class UserTypesListDataService implements FactoryInterface, ListDataInterface
{
    /**
     * RefData Service
     *
     * @var string
     */
    protected $refDataService;

    /**
     * Fetch list options
     *
     * @param array|string $context   Context
     * @param bool         $useGroups Use groups
     *
     * @return array
     */
    public function fetchListOptions($context = null, $useGroups = false)
    {
        return [
            'internal' => 'Internal',
            'local-authority' => 'Local authority',
            'operator' => 'Operator',
            'partner' => 'Partner',
            'transport-manager' => 'Transport Manager / Operator with TM access',
        ];
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setRefDataService($serviceLocator->get('\Common\Service\Data\RefData'));

        return $this;
    }

    /**
     * Set RefData service
     *
     * @param \Common\Service\Data\RefData $refDataService RefData service
     *
     * @return $this
     */
    public function setRefDataService($refDataService)
    {
        $this->refDataService = $refDataService;

        return $this;
    }

    /**
     * Get RefData service
     *
     * @return \Common\Service\Data\RefData
     */
    public function getRefDataService()
    {
        return $this->refDataService;
    }
}
