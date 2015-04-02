<?php

/**
 * Reprint Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;

/**
 * Reprint Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReprintDisc implements BusinessServiceInterface, BusinessServiceAwareInterface
{
    use BusinessServiceAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $ids = $params['ids'];

        $ceaseDiscService = $this->getBusinessServiceManager()->get('Lva\CeaseActiveDisc');
        $requestDiscService = $this->getBusinessServiceManager()->get('Lva\RequestDisc');

        foreach ($ids as $id) {
            $response = $ceaseDiscService->process(['id' => $id]);

            if (!$response->isOk()) {
                return $response;
            }

            $response = $requestDiscService->process(['licenceVehicle' => $id, 'isCopy' => 'Y']);

            if (!$response->isOk()) {
                return $response;
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}
