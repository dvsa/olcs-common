<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * F_Standing_FirstVeh bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FstandingFirstVeh extends DynamicBookmark implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'FinancialStandingRate',
            'data' => [
                'goodsOrPsv' => $data['goodsOrPsv'],
                'licenceType' => $data['licenceType'],
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
        if (isset($this->data['Results'][0]['firstVehicleFee'])) {
            return number_format($this->data['Results'][0]['firstVehicleFee']);
        }
        return '';
    }
}
