<?php

namespace Common\Service\Data;

use Common\Exception\DataServiceException;
use Dvsa\Olcs\Transfer\Query\Fee\GetLatestFeeType;

/**
 * Fee Type Data Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FeeTypeDataService extends AbstractDataService
{
    const FEE_TYPE_APP = 'APP';
    const FEE_TYPE_VAR = 'VAR';
    const FEE_TYPE_GRANT = 'GRANT';
    const FEE_TYPE_CONT = 'CONT';
    const FEE_TYPE_VEH = 'VEH';
    const FEE_TYPE_GRANTINT = 'GRANTINT';
    const FEE_TYPE_INTVEH = 'INTVEH';
    const FEE_TYPE_DUP = 'DUP';
    const FEE_TYPE_ANN = 'ANN';
    const FEE_TYPE_GRANTVAR = 'GRANTVAR';
    const FEE_TYPE_BUSAPP = 'BUSAPP';
    const FEE_TYPE_BUSVAR = 'BUSVAR';
    const FEE_TYPE_GVANNVEH = 'GVANNVEH';
    const FEE_TYPE_INTUPGRADEVEH = 'INTUPGRADEVEH';
    const FEE_TYPE_INTAMENDED = 'INTAMENDED';
    const FEE_TYPE_IRFOPSVAPP = 'IRFOPSVAPP';
    const FEE_TYPE_IRFOPSVANN = 'IRFOPSVANN';
    const FEE_TYPE_IRFOPSVCOPY = 'IRFOPSVCOPY';
    const FEE_TYPE_IRFOGVPERMIT = 'IRFOGVPERMIT';

    const ACCRUAL_RULE_LICENCE_START = 'acr_licence_start';
    const ACCRUAL_RULE_CONTINUATION  = 'acr_continuation';
    const ACCRUAL_RULE_IMMEDIATE     = 'acr_immediate';

    /**
     * Get latest fee types
     *
     * @param string $feeType     Fee type
     * @param string $goodsOrPsv  Goods or psv
     * @param string $licenceType Licence type
     * @param string $date        Date
     * @param string $trafficArea Traffic area
     *
     * @return array
     */
    public function getLatest($feeType, $goodsOrPsv, $licenceType = null, $date = null, $trafficArea = null)
    {
        $dtoData = GetLatestFeeType::create(
            [
                'feeType' => $feeType,
                'operatorType' => $goodsOrPsv,
                'licenceType' => $licenceType,
                'date' => $date,
                'trafficArea' => $trafficArea
            ]
        );

        $response = $this->handleQuery($dtoData);

        if ($response->isServerError() || $response->isClientError() || !$response->isOk()) {
            throw new DataServiceException('unknown-error');
        }

        return $this->formatResult($response->getResult());
    }
}
