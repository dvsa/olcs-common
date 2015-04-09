<?php

/**
 * Delete Transport Manager Licence by creating a delta
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\Response;

/**
 * Delete Transport Manager Licence by creating a delta
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeltaDeleteTransportManagerLicence implements
    BusinessServiceInterface,
    ServiceLocatorAwareInterface,
    BusinessServiceAwareInterface
{
    use ServiceLocatorAwareTrait,
        BusinessServiceAwareTrait;

    /**
     * Delete Transport Manager Licence by creating a delta
     *
     * @param array $params ['applicationId', 'transportManagerLicenceId']
     * @return ResponseInterface
     */
    public function process(array $params)
    {
        // validate applicationId param
        if (!isset($params['applicationId'])) {
            throw new \InvalidArgumentException('params key "applicationId" must be set');
        }
        $applicationId = (int) $params['applicationId'];

        // validate transportManagerLicenceId param
        if (!isset($params['transportManagerLicenceId'])) {
            throw new \InvalidArgumentException('params key "transportManagerLicenceId" must be set');
        }
        $transportManagerLicenceId = (int) $params['transportManagerLicenceId'];

        // @todo MATTY

        // create the transport manager application row with action D (delete)
        $tmaBusinessService = $this->getBusinessServiceManager()->get('Lva\TransportManagerApplication');
        $tma = [
            'application' => $applicationId,
            'transportManager' => $transportManagerLicenceId,
            'action' => 'D',
        ];
        $response = $tmaBusinessService->process(['data' => $tma]);

        return $response;
    }
}
