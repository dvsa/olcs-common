<?php

namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class AddressListDataService
 * @package Common\Service\Data
 */
class AddressListDataService implements ListDataInterface, ServiceLocatorAwareInterface
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
     * @param $category
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        $data = array();
        if (is_array($context['services'])) {
            $dataServiceManager = $this->getServiceLocator()->get('DataServiceManager');
            $formatter = new \Common\Service\Table\Formatter\Address();
            foreach ($context['services'] as $service) {
                $serviceName = 'Common\Service\Data\\' . ucfirst($service);
                $dataService = $dataServiceManager->get($serviceName);

                if (!($dataService instanceof AddressProviderInterface)) {
                    throw new \LogicException($serviceName . ' does not implement AddressProviderInterface');
                }
                $addressData = $dataService->fetchAddressListData();

                foreach ($addressData as $address) {
                    $data[$address['id']] = $formatter->format($address);
                }
            }
        }

        return $data;
    }
}
