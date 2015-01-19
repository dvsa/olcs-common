<?php

/**
 * Fee Type Data Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Entity\TrafficAreaEntityService;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;

/**
 * Fee Type Data Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FeeTypeDataService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

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
    const FEE_TYPE_BUSAPPSCOT = 'BUSAPPSCOT';
    const FEE_TYPE_BUSVARSCOT = 'BUSVARSCOT';
    const FEE_TYPE_IRFOPSVAPP = 'IRFOPSVAPP';
    const FEE_TYPE_IRFOPSVANN = 'IRFOPSVANN';
    const FEE_TYPE_IRFOPSVCOPY = 'IRFOPSVCOPY';
    const FEE_TYPE_IRFOGVPERMIT = 'IRFOGVPERMIT';

    const ACCRUAL_RULE_LICENCE_START = 'acr_licence_start';
    const ACCRUAL_RULE_CONTINUATION  = 'acr_continuation';
    const ACCRUAL_RULE_IMMEDIATE     = 'acr_immediate';

    protected $dataBundle = array(
        'properties' => array(
            'id',
            'fixedValue',
            'fiveYearValue',
            'description'
        )
    );

    public function getLatest($feeType, $goodsOrPsv, $licenceType, $date, $isNi = false)
    {
        $query = array(
            'feeType' => $feeType,
            'goodsOrPsv' => $goodsOrPsv,
            'licenceType' => array(
                $licenceType,
                'NULL'
            ),
            'effectiveFrom' => '<= ' . $date,
            'trafficArea' => $isNi ? TrafficAreaEntityService::NORTHERN_IRELAND_TRAFFIC_AREA_CODE : 'NULL',
            'sort' => 'effectiveFrom',
            'order' => 'DESC',
            'limit' => 1
        );

        $restHelper = $this->getServiceLocator()->get('Helper\Rest');

        $data = $restHelper->makeRestCall('FeeType', 'GET', $query, $this->dataBundle);

        if (!isset($data['Results'][0])) {
            throw new UnexpectedResponseException('No fee type found');
        }

        return $data['Results'][0];
    }
}
