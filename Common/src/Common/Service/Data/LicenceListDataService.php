<?php

namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\FactoryInterface;

/**
 * Class LicenceListDataService
 * @package Common\Service\Data
 */
class LicenceListDataService implements FactoryInterface, ListDataInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

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
     * @param $context
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        $data = array();

        if ($context == 'operatingCentre') {
            $data = $this->fetchOperatingCentreListOptions();
        }

        return $data;
    }

    protected function fetchOperatingCentreListOptions()
    {
        $data = array();
        $serviceName = 'Common\Service\Data\LicenceOperatingCentre';
        $licenceOcDataService = $this->getServiceLocator()->get($serviceName);
        $rawData = $licenceOcDataService->fetchData();

        foreach ($rawData as $id => $operatingCentre) {
            $data[$id] = $operatingCentre;
        }

        return $data;
    }

    /**
     * Returns the bundle required to retrieve the data for the list options
     * @param $context
     */
    protected function getContextBundle($context)
    {
        $bundle = array();
        if ($context == 'operatingCentre') {
            $bundle = array(
                'properties' => 'ALL',
                'children' => array(
                    'operatingCentres' => array(
                        'properties' => 'ALL',
                        'children' => array(

                        )
                    ),
                    'organisation' => array(
                        'properties' => 'ALL',
                        'children' => array(
                            'organisationPersons' => array(
                                'properties' => 'ALL'
                            ),
                            'tradingNames' => array(
                                'properties' => 'ALL'
                            )
                        )
                    )
                )
            );

        }
        return $bundle;
    }
}
