<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Entity\FeeTypeEntityService;
use Common\Service\Entity\TrafficAreaEntityService;

/**
 * TotalContFee bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TotalContFee extends DynamicBookmark implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'FeeType',
            'data' => [
                'feeType' => FeeTypeEntityService::FEE_TYPE_CONTINUATION,
                'goodsOrPsv' => $data['goodsOrPsv'],
                'licenceType' => [
                    $data['licenceType'],
                    'NULL'
                ],
                'trafficAreaId' => ($data['niFlag'] === 'Y') ?
                    TrafficAreaEntityService::NORTHERN_IRELAND_TRAFFIC_AREA_CODE :
                    '!= ' . TrafficAreaEntityService::NORTHERN_IRELAND_TRAFFIC_AREA_CODE,
                'effectiveFrom' => '<= ' . $this->getServiceLocator()->get('Helper\Date')->getDate(\DateTime::W3C),
                'sort' => 'effectiveFrom',
                'order' => 'DESC',
                'limit' => 1
            ],
            'bundle' => []
        ];
        return $query;
    }

    public function render()
    {
        if (isset($this->data['Results'][0])) {
            $value = $this->data['Results'][0]['fixedValue'] ? $this->data['Results'][0]['fixedValue'] :
                $this->data['Results'][0]['fiveYearValue'];
            return number_format($value);
        }
        return '';
    }
}
