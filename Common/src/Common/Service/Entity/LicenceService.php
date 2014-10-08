<?php

/**
 * Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceService extends AbstractEntityService
{
    /**
     * Goods or PSV keys
     */
    const LICENCE_CATEGORY_GOODS_VEHICLE = 'lcat_gv';
    const LICENCE_CATEGORY_PSV = 'lcat_psv';

    /**
     * Licence types keys
     */
    const LICENCE_TYPE_RESTRICTED = 'ltyp_r';
    const LICENCE_TYPE_STANDARD_INTERNATIONAL = 'ltyp_si';
    const LICENCE_TYPE_STANDARD_NATIONAL = 'ltyp_sn';
    const LICENCE_TYPE_SPECIAL_RESTRICTED = 'ltyp_sr';

    const LICENCE_STATUS_NEW = 'lsts_new';
    const LICENCE_STATUS_SUSPENDED = 'lsts_suspended';
    const LICENCE_STATUS_VALID = 'lsts_valid';
    const LICENCE_STATUS_CURTAILED = 'lsts_curtailed';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Licence';

    /**
     * Holds the overview bundle
     *
     * @var array
     */
    private $overviewBundle = array(
        'properties' => array(
            'id',
            'grantedDate',
            'expiryDate',
            'niFlag',
            'licNo'
        ),
        'children' => array(
            'status' => array(
                'properties' => array('id')
            ),
            'goodsOrPsv' => array(
                'properties' => array('id')
            ),
            'licenceType' => array(
                'properties' => array('id')
            )
        )
    );

    /**
     * Holds the bundle to retrieve type of licence bundle
     *
     * @var array
     */
    private $typeOfLicenceBundle = array(
        'properties' => array(
            'version',
            'niFlag'
        ),
        'children' => array(
            'goodsOrPsv' => array(
                'properties' => array('id')
            ),
            'licenceType' => array(
                'properties' => array('id')
            )
        )
    );

    /**
     * Get data for overview
     *
     * @param int $id
     * @return array
     */
    public function getOverview($id)
    {
        return $this->getHelperService('RestHelper')
            ->makeRestCall($this->entity, 'GET', $id, $this->overviewBundle);
    }

    /**
     * Get type of licence data
     *
     * @param int $id
     * @return array
     */
    public function getTypeOfLicenceData($id)
    {
        $data = $this->getHelperService('RestHelper')
            ->makeRestCall($this->entity, 'GET', $id, $this->typeOfLicenceBundle);

        return array(
            'version' => $data['version'],
            'niFlag' => $data['niFlag'],
            'licenceType' => isset($data['licenceType']['id']) ? $data['licenceType']['id'] : null,
            'goodsOrPsv' => isset($data['goodsOrPsv']['id']) ? $data['goodsOrPsv']['id'] : null
        );
    }
}
