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
            'previousLicenceType',
            'role',
            'transportManagerApplication',
            'transportManagerLicence'
        ]
    ];

    protected $licenceTableBundle = array(
        'children' => array(
            'previousLicenceType'
        )
    );

    public function getForApplicationAndType($applicationId, $prevLicenceType)
    {
        $data = $this->getAll(
            array('application' => $applicationId, 'previousLicenceType' => $prevLicenceType),
            $this->licenceTableBundle
        );

        return $data['Results'];
    }

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

    public function getById($id)
    {
        return $this->get($id, $this->bundle);
    }
}
