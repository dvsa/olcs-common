<?php

namespace Common\Controller\Lva\Traits;

use Dvsa\Olcs\Transfer\Query\TransportManagerApplication\GetDetails;

trait TransportManagerApplicationTrait
{
    protected $tma;

    public function preDispatch()
    {
        $tmaId = (int)$this->params('child_id');
        $this->tma = $this->getTransportManagerApplication($tmaId);
        $this->lva = $this->returnApplicationOrVariation();
    }

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

    /**
     * Returns "application" or "variation"
     *
     * @param array $tma
     *
     * @return string
     */
    protected function returnApplicationOrVariation(): string
    {
        if ($this->tma["application"]["isVariation"]) {
            return self::LVA_VAR;
        }
        return self::LVA_APP;
    }

}
