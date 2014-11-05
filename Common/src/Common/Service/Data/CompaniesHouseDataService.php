<?php

namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Companies house data service
 */
class CompaniesHouseDataService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function search($type, $value)
    {
        return $this->getServiceLocator()->get('Helper\Rest')->makeRestCall(
            'CompaniesHouse',
            'GET',
            [
                'type' => $type,
                'value' => $value
            ]
        );
    }
}
