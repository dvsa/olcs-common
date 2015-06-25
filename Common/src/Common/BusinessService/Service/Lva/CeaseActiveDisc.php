<?php

/**
 * Cease Active Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Cease Active Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @todo maybe delete me
 */
class CeaseActiveDisc implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $id = $params['id'];

        $results = $this->getServiceLocator()->get('Entity\LicenceVehicle')->getActiveDiscs($id);

        if (!empty($results['goodsDiscs'])) {
            $activeDisc = $results['goodsDiscs'][0];

            if (empty($activeDisc['ceasedDate'])) {
                $activeDisc['ceasedDate'] = $this->getServiceLocator()->get('Helper\Date')->getDate(\DateTime::W3C);
                $this->getServiceLocator()->get('Entity\GoodsDisc')->save($activeDisc);
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}
