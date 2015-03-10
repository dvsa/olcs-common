<?php

/**
 * Previous Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Previous Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PreviousLicenceEntityService extends AbstractEntityService
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
    protected $entity = 'PreviousLicence';

    protected $licenceTableBundle = array(
        'children' => array(
            'previousLicenceType'
        )
    );

    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
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

    public function getById($id)
    {
        return $this->get($id, $this->dataBundle);
    }
}
