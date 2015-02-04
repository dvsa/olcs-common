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
     * Get data
     *
     * @param int $tid
     * @return array
     */
    public function getData($id)
    {
        return $this->get($id);
    }
}
