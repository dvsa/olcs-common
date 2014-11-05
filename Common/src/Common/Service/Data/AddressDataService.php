<?php

/**
 * Address Data Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Address Data Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddressDataService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function getAddressForUprn($uprn)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->sendGet('postcode\address', array('id' => $uprn), true);
    }

    public function getAddressesForPostcode($postcode)
    {
        return $this->getServiceLocator()->get('Helper\Rest')
            ->sendGet('postcode\address', array('postcode' => $postcode), true);
    }
}
