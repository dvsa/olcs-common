<?php

/**
 * Other Licence Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Other Licence Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OtherLicenceEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'OtherLicence';

    /**
     * Default bundle
     *
     * @var array
     */
    protected $bundle = [
        'children' => [
            'role'
        ]
    ];

    /**
     * Get data for tansport manager
     *
     * @param int $transportManagerId
     * @return array
     */
    public function getDataForTransportManager($transportManagerId)
    {
        return $this->get(array('transportManager' => $transportManagerId))['Results'];
    }
    
    /**
     * Get data for tansport manager application
     *
     * @param int $id
     * @return array
     */
    public function getByTmApplicationId($id)
    {
        return $this->get(array('transportManagerApplication' => $id), $this->bundle)['Results'];
    }

    /**
     * Get data for tansport manager licence
     *
     * @param int $id
     * @return array
     */
    public function getByTmLicenceId($id)
    {
        return $this->get(array('transportManagerLicence' => $id), $this->bundle)['Results'];
    }
}
