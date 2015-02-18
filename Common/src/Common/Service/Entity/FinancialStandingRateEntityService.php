<?php

/**
 * Financial Standing Rate Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Entity;

/**
 * Financial Standing Rate Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialStandingRateEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'FinancialStandingRate';

    protected $ratesBundle = [
        'children' => [ 'goodsOrPsv', 'licenceType' ]
    ];

    /**
     * Get all current rates
     */
    public function getRatesInEffect($date = null)
    {
        if (is_null($date)) {
            $date = $this->getServiceLocator()->get('Helper\Date')->getDate();
        }
        $query = [
            'effectiveFrom' => '<='.$date,
            'deletedDate' => 'NULL',
            // in case old rates have not yet been deleted, we'll sort to return
            // the most up-to-date data first
            'sort' => 'effectiveFrom',
            'order' => 'DESC',
        ];

        $data = $this->getServiceLocator()->get('Entity\FinancialStandingRate')
            ->get($query, $this->ratesBundle);

        return $data['Results'];
    }
}
