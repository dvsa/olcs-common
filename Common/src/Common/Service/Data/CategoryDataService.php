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
class CategoryDataService implements FactoryInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait,
        RestCallTrait;

    const CATEGORY_SERVICE = 'Category';
    const SUB_CATEGORY_SERVICE = 'SubCategory';

    const CATEGORY_LICENSING = 1;
    const CATEGORY_COMPLIANCE = 2;
    const CATEGORY_BUS_REGISTRATION = 3;
    const CATEGORY_PERMITS = 4;
    const CATEGORY_TRANSPORT_MANAGER = 5;
    const CATEGORY_ENVIRONMENTAL = 7;
    const CATEGORY_IRFO = 8;
    const CATEGORY_APPLICATION = 9;
    const CATEGORY_SUBMISSION = 10;

    // @NOTE create constants for all sub categories as required. Only a subset
    // will ever be needed programatically so this list should be manageable
    const DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL = 5;
    const TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE = 11;
    const TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL = 15;
    const TASK_SUB_CATEGORY_HEARINGS_APPEALS = 49;
    const SCAN_SUB_CATEGORY_CHANGE_OF_ENTITY = 85;
    const DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST = 91;
    const DOC_SUB_CATEGORY_LICENCE_INSOLVENCY_DOCUMENT_DIGITAL = 112;
    const DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION = 98;

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

        $params = $this->getParams($description, $subCategoryType);

        $cached = $this->getFromCache($service, $params);

        if ($cached) {
            return $cached;
        }

        return $this->fetchCategory($service, $params);
    }

    /**
     * Fetch and cache the category
     *
     * @param string $service
     * @param array $params
     * @return string
     */
    private function fetchCategory($service, $params)
    {
        $data = $this->makeRestCall($service, 'GET', $params);

        if ($data['Count'] > 1) {
            $category = $data['Results'];
        } elseif ($data['Count'] == 1) {
            $category = $data['Results'][0];
        } else {
            $category = null;
        }

        $this->addToCache($service, $params, $category);

        return $category;
    }

    /**
     * Get service from sub category
     *
     * @param string $subCategoryType
     * @return string
     */
    private function getServiceFromSubCategoryType($subCategoryType = null)
    {
        if ($subCategoryType === null) {
            return self::CATEGORY_SERVICE;
        }

        return self::SUB_CATEGORY_SERVICE;
    }

    /**
     * Get query params from sub category
     *
     * @param string $subCategoryType
     * @return string
     */
    private function getParams($description, $subCategoryType = null)
    {
        if ($subCategoryType === null) {
            return ['description' => $description];
        }

        return [
            'subCategoryName' => $description,
            $this->getRestriction($subCategoryType) => true
        ];
    }

    /**
     * Map a user friendly description to a query restriction
     */
    private function getRestriction($subCategoryType)
    {
        switch ($subCategoryType) {
            case 'Document':
                return 'isDoc';
            case 'Task':
                return 'isTask';
            case 'Scan':
                return 'isScan';
            default:
                return null;
        }
    }

    /**
     * Check if we have this category cached
     *
     * @param string $service
     * @param array $params
     * @return string|null
     */
    private function getFromCache($service, $params)
    {
        $description = implode("|", $params);
        return (isset($this->cache[$service][$description]) ? $this->cache[$service][$description] : null);
    }

    /**
     * Check if we have this category cached
     *
     * @param string $service
     * @param array $params
     * @param array $category
     */
    private function addToCache($service, $params, $category)
    {
        $description = implode("|", $params);
        $this->cache[$service][$description] = $category;
    }
}
