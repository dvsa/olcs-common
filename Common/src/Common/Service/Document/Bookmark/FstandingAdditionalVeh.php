<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Document\Bookmark\Interfaces\DateHelperAwareInterface;
use Common\Service\Helper\DateHelperService;

/**
 * F_Standing_AdditionalVeh bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FstandingAdditionalVeh extends DynamicBookmark implements DateHelperAwareInterface
{
    private $helper;

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'FinancialStandingRate',
            'data' => [
                'goodsOrPsv' => $data['goodsOrPsv'],
                'licenceType' => $data['licenceType'],
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
        if (isset($this->data['Results'][0]['additionalVehicleRate'])) {
            return number_format($this->data['Results'][0]['additionalVehicleRate']);
        }
        return '';
    }

    public function setDateHelper(DateHelperService $helper)
    {
        $this->helper = $helper;
    }
}
