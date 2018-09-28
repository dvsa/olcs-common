<?php

namespace Common\Controller\Lva\Traits;

use Dvsa\Olcs\Transfer\Query\TransportManagerApplication\GetDetails;

trait TransportManagerApplicationTrait
{

    /**
     * getTransportManagerApplication
     *
     * @param int $transportManagerApplicationId
     *
     * @return array|mixed
     */
    protected function getTransportManagerApplication($transportManagerApplicationId): array
    {
        $transportManagerApplication = $this->handleQuery(
            GetDetails::create(['id' => $transportManagerApplicationId])
        )->getResult();
        return $transportManagerApplication;
    }

    /**
     * getTmName
     *
     * @param array $transportManagerApplication
     *
     * @return string
     */
    protected function getTmName(array $transportManagerApplication)
    {
        return trim(
            $transportManagerApplication['transportManager']['homeCd']['person']['forename'] . ' '
            . $transportManagerApplication['transportManager']['homeCd']['person']['familyName']
        );
    }

}
