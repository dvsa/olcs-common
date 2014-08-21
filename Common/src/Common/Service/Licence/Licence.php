<?php

/**
 * Licence generation and updating
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Licence;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Licence generation and updating
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Licence implements ServiceLocatorAwareInterface
{
    use \Common\Util\RestCallTrait;

    const GOODS_OR_PSV_PSV = 'lcat_psv';
    const GOODS_OR_PSV_GOODS_VEHICLE = 'lcat_gv';

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Generates new licences or updates existing one and saves it to licence entity
     * 
     * @param string $applicationId
     * @return string|bool
     */
    public function generateLicence($applicationId = null)
    {
        $newLicenceNumber = false;
        if ($applicationId) {
            $bundle = array(
                'properties' => null,
                'children' => array(
                    'licence' => array(
                        'properties' => array(
                            'id',
                            'licNo',
                            'version'
                        ),
                        'children' => array(
                            'trafficArea' => array(
                                'properties' => array(
                                    'id',
                                )
                            ),
                            'goodsOrPsv' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    )
                )
            );

            $application = $this->makeRestCall('Application', 'GET', array('id' => $applicationId), $bundle);

            // for testing
            $application['licence']['trafficArea']['id'] = 'K';

            if (isset($application['licence']['goodsOrPsv']['id']) && $application['licence']['goodsOrPsv']['id'] &&
                in_array(
                    $application['licence']['goodsOrPsv']['id'],
                    array(self::GOODS_OR_PSV_PSV, self::GOODS_OR_PSV_GOODS_VEHICLE)
                ) &&
                isset($application['licence']['trafficArea']['id']) && $application['licence']['trafficArea']['id']) {

                // processing new licence
                if (!$application['licence']['licNo']) {

                    $licenceGen = $this->makeRestCall('LicenceNoGen', 'POST', array('application' => $applicationId));

                    if (isset($licenceGen['id']) ) {

                        if ($application['licence']['goodsOrPsv']['id'] == self::GOODS_OR_PSV_PSV) {
                            $newLicenceNumber = 'P' . $application['licence']['trafficArea']['id'] . $licenceGen['id'];
                        } else {
                            $newLicenceNumber = 'O' . $application['licence']['trafficArea']['id'] . $licenceGen['id'];
                        }
                        $this->makeRestCall(
                            'Licence',
                            'PUT',
                            array(
                                'id' => $application['licence']['id'],
                                'licNo' => $newLicenceNumber,
                                'version' => $application['licence']['version']
                            )
                        );
                    }
                } else {
                    // processing existing licence
                    $previousTrafficAreaCode = substr($application['licence']['licNo'], 1, 1);
                    if ($previousTrafficAreaCode != $application['licence']['trafficArea']['id']) {
                        $newLicenceNumber = substr($application['licence']['licNo'], 0, 1) .
                            $application['licence']['trafficArea']['id'] .
                            substr($application['licence']['licNo'], 2);

                        $this->makeRestCall(
                            'Licence',
                            'PUT',
                            array(
                                'id' => $application['licence']['id'],
                                'licNo' => $newLicenceNumber,
                                'version' => $application['licence']['version']
                            )
                        );

                    }
                }
            }

        }

        return $newLicenceNumber;
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
