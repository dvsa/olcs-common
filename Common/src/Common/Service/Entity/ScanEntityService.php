<?php

/**
 * Scan Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Scan Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ScanEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Scan';

    /**
     * Holds the main data bundle
     *
     * @var array
     */
    protected $mainBundle = [
        'children' => [
            'licence', 'busReg', 'case',
            'transportManager', 'category',
            'subCategory', 'irfoOrganisation'
        ]
    ];

    /**
     * Retrieve a scan by its primary identifier
     */
    public function findById($id)
    {
        return $this->get($id, $this->mainBundle);
    }

    /*
     * Our corresponding processing service makes use of these
     */
    public function getChildRelations()
    {
        return $this->mainBundle['children'];
    }
}
