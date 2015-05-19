<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Document\Bookmark\Interfaces\DateHelperAwareInterface;
use Common\Service\Helper\DateHelperService;
use Common\Service\Entity\FeeTypeEntityService;
use Common\Service\Entity\TrafficAreaEntityService;

/**
 * TotalContFee bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TotalContFee extends DynamicBookmark implements DateHelperAwareInterface
{
    private $helper;

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
                'effectiveFrom' => '<= ' . $this->helper->getDate('Y-m-d'),
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
            $value = (int) $this->data['Results'][0]['fixedValue'] ? $this->data['Results'][0]['fixedValue'] :
                $this->data['Results'][0]['fiveYearValue'];
            return number_format($value);
        }
        return '';
    }

    public function setDateHelper(DateHelperService $helper)
    {
        $this->helper = $helper;
    }
}
