<?php

namespace Common\Rbac\Role;

use Rbac\Role\Role;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Role\RoleProviderInterface;

/**
 * Class RoleProvider
 * @package Common\Rbac\Role
 */
class RoleProvider implements RoleProviderInterface, FactoryInterface
{
    const CACHE_KEY = 'rbac.roles';
    const MAX_ROLES = 50;

    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cache;

    /**
     * @var \Common\Service\Data\Interfaces\DataService
     */
    protected $dataService;

    /**
     * @param \Zend\Cache\Storage\StorageInterface $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param \Common\Service\Data\Interfaces\DataService $dataService
     */
    public function setDataService($dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * @return \Common\Service\Data\Interfaces\DataService
     */
    public function getDataService()
    {
        return $this->dataService;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $this->setDataService($serviceLocator->get('DataServiceManager')->get('Generic\Service\Data\Role'));
        $this->setCache($serviceLocator->get('Cache'));
        return $this;
    }

    /**
     * Get the roles from the provider
     *
     * @param  string[] $roleNames
     * @return \Rbac\Role\RoleInterface[]
     */
    public function getRoles(array $roleNames)
    {
        $success = false;
        $result = $this->getCache()->getItem(self::CACHE_KEY, $success);

        if (!$success) {

            $result = [];
            $data = $this->getDataService()->fetchList(['limit' => self::MAX_ROLES]);

            foreach ($data as $roleData) {
                $role = new Role($roleData['role']);

                if (isset($roleData['rolePermissions'])) {
                    foreach ($roleData['rolePermissions'] as $permission) {
                        $role->addPermission($permission['permission']['name']);
                    }
                }

                $result[$roleData['role']] = $role;
            }
            $this->getCache()->setItem(self::CACHE_KEY, $result);
        }

        return array_intersect_key($result, array_combine($roleNames, $roleNames));
    }
}
