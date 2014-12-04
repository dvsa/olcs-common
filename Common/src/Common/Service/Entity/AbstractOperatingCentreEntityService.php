<?php

/**
 * Abstract Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

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
                    'adDocuments'
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

    public function getAddressSummaryData($lvaId)
    {
        return $this->getAll(array($this->type => $lvaId), $this->addressSummaryBundle);
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
