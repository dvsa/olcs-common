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
                // yes, 'administritive_area' really is mis-spelled in API response :(
                if (isset($response[0]['administritive_area'])) {
                    $adminArea = $response[0]['administritive_area'];
                } elseif (isset($response['administritive_area'])) {
                    $adminArea = $response['administritive_area'];
                }
                if ($adminArea) {
                    $bundle = array(
                        'children' => array(
                            'trafficArea' => array()
                        )
                    );
                    $adminAreaTrafficArea = $this->makeRestCall(
                        'AdminAreaTrafficArea', 'GET', array('id' => $adminArea), $bundle
                    );
                    if (is_array($adminAreaTrafficArea)
                        && array_key_exists('trafficArea', $adminAreaTrafficArea) && count($adminAreaTrafficArea)
                    ) {
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
     * @param string $postcode expected format is 'LS9 6NF' (we rely on the space)
     * @return array|null
     */
    public function getEnforcementAreaByPostcode($postcode)
    {
        $matches = [];
        $ea = null;

        preg_match('/^([^\s]+)\s(\d).+$/', $postcode, $matches);

        if (!empty($matches)) {
            $prefix = $matches[1];
            $suffixDigit = $matches[2];
            $entityService = $this->getServiceLocator()->get('Entity\PostcodeEnforcementArea');

            // first try lookup by prefix + first digit of suffix
            $ea = $entityService->getEnforcementAreaByPostcodePrefix($prefix . ' ' . $suffixDigit);

            if (is_null($ea)) {
                // if not found, try by just the prefix
                $ea = $entityService->getEnforcementAreaByPostcodePrefix($prefix);
            }
        }

        return $ea;
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
