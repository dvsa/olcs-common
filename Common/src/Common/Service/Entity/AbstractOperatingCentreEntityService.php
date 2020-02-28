<?php

/**
 * Abstract Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

use Common\RefData;

/**
 * Abstract Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractOperatingCentreEntityService extends AbstractEntityService
{
    protected $type = null;
    /**
     * Address summary data
     *
     * @var array
     * @todo migrate
     */
    protected $addressSummaryBundle = array(
        'children' => array(
            'operatingCentre' => array(
                'children' => array(
                    'address' => array(
                        'children' => array(
                            'countryCode'
                        )
                    ),
                    'adDocuments',
                    'complaints' => array(
                        'criteria' => array(
                            'status' => RefData::COMPLAIN_STATUS_OPEN
                        )
                    )
                )
            )
        )
    );

    /**
     * Address data
     *
     * @var array
     */
    protected $addressBundle = array(
        'children' => array(
            'operatingCentre' => array(
                'children' => array(
                    'address' => array(
                        'children' => array(
                            'countryCode'
                        )
                    ),
                    'adDocuments'
                )
            )
        )
    );

    protected $listBundle = array(
        'children' => array(
            'operatingCentre'
        )
    );

    protected $operatingCentreListBundle = array(
        'children' => array(
            'operatingCentre' => array(
                'children' => array(
                    'address' => array(
                        'children' => array(
                            'countryCode'
                        )
                    )
                )
            )
        )
    );

    public function getOperatingCentreListForLva($lvaId)
    {
        return $this->getAll(array($this->type => $lvaId), $this->operatingCentreListBundle);
    }

    public function getListForLva($lvaId)
    {
        return $this->getAll(array($this->type => $lvaId), $this->listBundle);
    }

    /**
     * @todo migrate me
     */
    public function getAddressSummaryData($lvaId)
    {
        $query = array($this->type => $lvaId);

        // @todo Need to order by OC when we migrate to the backend
        // $query['sort'] = 'operatingCentre';

        return $this->getAll($query, $this->addressSummaryBundle);
    }

    public function getAddressData($id)
    {
        return $this->get($id, $this->addressBundle);
    }

    public function getOperatingCentresCount($lvaId)
    {
        return $this->get(array($this->type => $lvaId));
    }
}
