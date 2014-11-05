<?php

/**
 * Application Goods Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Common\Service\Entity\ApplicationEntityService;

/**
 * Application Goods Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ApplicationGoodsVehiclesControllerTrait
{
    /**
     * If we have the not-yet submitted status, then we should remove the reprint button
     *
     * @param \Common\Service\Table\TableBuilder
     */
    protected function alterTable($table)
    {
        $applicationStatus = $this->getServiceLocator()->get('Entity\Application')
            ->getStatus($this->getApplicationId());

        if ($applicationStatus == ApplicationEntityService::APPLICATION_STATUS_NOT_SUBMITTED) {
            $table->removeAction('reprint');
        }

        return $table;
    }
}
