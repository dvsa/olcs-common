<?php

namespace Common\Service\Data;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class EbsrSubTypeListDataService
 * Provides list options in EBSR submission type file upload
 *
 * @package Olcs\Service
 */
class EbsrSubTypeListDataService implements FactoryInterface, ListDataInterface
{
    /**
     * Ref data category ID for EBSR submission types
     */
    const EBSR_REF_DATA_CATEGORY_ID = 'ebsr_sub_type';

    const EBSR_SUB_TYPE_NEW_APPLICATION = 'ebsrt_new';
    const EBSR_SUB_TYPE_DATA_REFRESH = 'ebsrt_refresh';

    /**
     * RefData Service
     * @var string
     */
    protected $refDataService;

    /**
     * RefData Service
     * @var string
     */
    private $validSubmissionTypes = [self::EBSR_SUB_TYPE_NEW_APPLICATION, self::EBSR_SUB_TYPE_DATA_REFRESH];

    /**
     * Filters out all options but those allowable / implemented
     *
     * @param null $context
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context = null, $useGroups = false)
    {
        $allOptions =  $this->getRefDataService()->fetchListData(self::EBSR_REF_DATA_CATEGORY_ID);
        $options = [];
        if (is_array($allOptions)) {
            foreach ($allOptions as $option) {
                if (in_array($option['id'], $this->validSubmissionTypes)) {
                    $options[$option['id']] = $option['description'];
                }
            }
        }
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
