<?php

/**
 * Service to get traffic area by postcode
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Postcode;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Service to get traffic area by postcode
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Postcode implements ServiceLocatorAwareInterface
{
    use \Common\Util\RestCallTrait;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Get traffic area by postocde
     * 
     * @param string $postcode
     * @return array
     */
    public function getTrafficAreaByPostcode($postcode = null)
    {
        $retv = array(null, null);
        if ($postcode) {
            $response = $this->sendGet('postcode\address', array('postcode' => $postcode), true);
            if (is_array($response) && count($response)) {
                $adminArea = $response[0]['administritive_area'];
                if ($adminArea) {
                    $bundle = array(
                        'properties' => null,
                        'children' => array(
                            'trafficArea' => array(
                                'properties' => array(
                                    'id',
                                    'name'
                                )
                            )
                        )
                    );
                    $adminAreaTrafficArea = $this->makeRestCall(
                        'AdminAreaTrafficArea', 'GET', array('id' => $adminArea), $bundle
                    );
                    if (is_array($adminAreaTrafficArea) && array_key_exists('trafficArea', $adminAreaTrafficArea) && count($adminAreaTrafficArea)) {
                        $retv = array(
                            $adminAreaTrafficArea['trafficArea']['id'],
                            $adminAreaTrafficArea['trafficArea']['name']
                        );
                    }
                }
            }
        }
        return $retv;
    }

    /**
     * Set service locator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
