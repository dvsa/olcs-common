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
    const TYPE_CURRENT = 'prev_has_licence';
    const TYPE_APPLIED = 'prev_had_licence';
    const TYPE_REFUSED = 'prev_been_refused';
    const TYPE_REVOKED = 'prev_been_revoked';
    const TYPE_PUBLIC_INQUIRY = 'prev_been_at_pi';
    const TYPE_DISQUALIFIED = 'prev_been_disqualified_tc';
    const TYPE_HELD = 'prev_purchased_assets';

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

    public function getById($id)
    {
        return $this->get($id, $this->bundle);
    }
}
