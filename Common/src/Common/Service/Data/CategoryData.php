<?php

/**
 * Category Data Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\FactoryInterface;
use Common\Util\RestCallTrait;

/**
 * Category Data Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CategoryData implements FactoryInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait,
        RestCallTrait;

    const CATEGORY_SERVICE = 'Category';
    const SUB_CATEGORY_SUFFIX = 'SubCategory';

    /**
     * Cache the categories
     *
     * @var array
     */
    private $cache = array();

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);

        return $this;
    }

    /**
     * Get a category by its description
     *
     * @param string $description
     * @param string $subCategoryType
     * @return array
     */
    public function getCategoryByDescription($description, $subCategoryType = null)
    {
        $service = $this->getServiceFromSubCategoryType($subCategoryType);

        $cached = $this->getFromCache($service, $description);

        if ($cached) {
            return $cached;
        }

        return $this->fetchCategory($service, $description);
    }

    /**
     * Fetch and cache the category
     *
     * @param string $service
     * @param string $description
     * @return string
     */
    private function fetchCategory($service, $description)
    {
        $data = $this->makeRestCall($service, 'GET', array('description' => $description));

        if ($data['Count'] > 1) {
            $category = $data['Results'];
        } elseif ($data['Count'] == 1) {
            $category = $data['Results'][0];
        } else {
            $category = null;
        }

        $this->addToCache($service, $description, $category);

        return $category;
    }

    /**
     * Get service form sub category
     *
     * @param string $subCategoryType
     * @return string
     */
    private function getServiceFromSubCategoryType($subCategoryType = null)
    {
        if ($subCategoryType === null) {
            return self::CATEGORY_SERVICE;
        }

        return ucfirst($subCategoryType) . self::SUB_CATEGORY_SUFFIX;
    }

    /**
     * Check if we have this category cached
     *
     * @param string $service
     * @param string $description
     * @return string|null
     */
    private function getFromCache($service, $description)
    {
        return (isset($this->cache[$service][$description]) ? $this->cache[$service][$description] : null);
    }

    /**
     * Check if we have this category cached
     *
     * @param string $service
     * @param string $description
     * @param array $category
     */
    private function addToCache($service, $description, $category)
    {
        $this->cache[$service][$description] = $category;
    }
}
