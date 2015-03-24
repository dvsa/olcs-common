<?php

namespace Common\Service\Data;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserTypesListDataService
 * Provides list options for user types
 *
 * @package Olcs\Service
 */
class UserTypesListDataService implements FactoryInterface, ListDataInterface
{

    /**
     * RefData Service
     * @var string
     */
    protected $refDataService;

    /**
     * Filters out all options but those allowable / implemented
     *
     * @param null $context
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context = null, $useGroups = false)
    {
        $options = [
            'internal-limited-read-only' => 'Internal - Limited Read only',
            'internal-read-only' => 'Internal - Read only',
            'internal-case-worker' => 'Internal - Case worker',
            'internal-admin' => 'Internal - Admin',
            'operator-admin' => 'External - Admin',
            'operator-user' => 'Internal - Case worker',
            'operator-tm' => 'Internal - Case worker',
            'operator-ebsr' => 'Internal - Case worker',
            'partner-admin' => 'Internal - Case worker',
            'partner-user' => 'Internal - Case worker',
            'local-authority-admin' => 'Internal - Case worker',
            'local-authority-user' => 'Internal - Case worker'
        ];
        return $options;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setRefDataService($serviceLocator->get('\Common\Service\Data\RefData'));

        return $this;
    }

    /**
     * @param string $refDataService
     */
    public function setRefDataService($refDataService)
    {
        $this->refDataService = $refDataService;
    }

    /**
     * @return string
     */
    public function getRefDataService()
    {
        return $this->refDataService;
    }
}
