<?php

namespace Common\Service\Data\Search;

use Common\Service\Data\Interfaces\ListData as ListDataInterface;
use Common\Service\Data\Search\SearchTypeManager;
use Laminas\Navigation\Service\AbstractNavigationFactory;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class SearchType
 * @package Olcs\Service\Data\Search
 */
class SearchType implements ListDataInterface, FactoryInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $searchTypeManager;

    /**
     * @var AbstractNavigationFactory
     */
    protected $navigationFactory;

    /**
     * @return mixed
     */
    public function getSearchTypeManager()
    {
        return $this->searchTypeManager;
    }

    /**
     * @param mixed $searchTypeManager
     */
    public function setSearchTypeManager($searchTypeManager)
    {
        $this->searchTypeManager = $searchTypeManager;
    }

    /**
     * @return mixed
     */
    public function getNavigationFactory()
    {
        return $this->navigationFactory;
    }

    /**
     * @param mixed $navigationFactory
     */
    public function setNavigationFactory($navigationFactory)
    {
        $this->navigationFactory = $navigationFactory;
    }

    /**
     * Fetch back a set of options for a drop down list, context passed is parameters which may need to be passed to the
     * back end to filter the result set returned, use groups when specified should, cause this method to return the
     * data as a multi dimensioned array suitable for display in opt-groups. It is permissible for the method to ignore
     * this flag if the data doesn't allow for option groups to be constructed.
     *
     * @param mixed $context
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        $options = [];

        foreach ($this->getSearchTypes() as $searchIndex) {
            /** @var $searchIndex \Common\Data\Object\Search\SearchAbstract  */
            if ($context === null || $searchIndex->getDisplayGroup() === $context) {
                $options[$searchIndex->getKey()] = $searchIndex->getTitle();
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function getSearchTypes()
    {
        $services = $this->getSearchTypeManager()->getRegisteredServices();

        $indexes = [];

        foreach (array_merge($services['factories'], $services['invokableClasses']) as $searchIndexName) {
            $indexes[] = $this->getSearchTypeManager()->get($searchIndexName);
        }

        return $indexes;
    }

    /**
     * @return \Laminas\Navigation\Navigation
     */
    public function getNavigation($context = null, array $queryParams = [])
    {
        $nav = [];
        foreach ($this->getSearchTypes() as $searchIndex) {
            /** @var \Common\Data\Object\Search\SearchAbstract $searchIndex */
            if ($context === null || $searchIndex->getDisplayGroup() === $context) {
                $nav[] = $searchIndex->getNavigation($queryParams);
            }
        }

        return $this->getNavigationFactory()->getNavigation($nav);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setNavigationFactory($serviceLocator->get('NavigationFactory'));
        $this->setSearchTypeManager($serviceLocator->get(SearchTypeManager::class));

        return $this;
    }
}
