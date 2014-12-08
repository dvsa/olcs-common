<?php

/**
 * Address List Data Service, used to extract a list of addresses for a given context.
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Address List Data Service
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class AddressListDataService implements ServiceLocatorAwareInterface, ListDataInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @param $category
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($category, $useGroups = false)
    {
        $data[1] = 'Test address 1, testTown';
        $data[2] = 'Test address 2, testTown';
        $data[3] = 'Test address 3, testTown';

        return $data;
    }
}
