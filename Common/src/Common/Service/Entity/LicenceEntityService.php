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
class LicenceEntityService extends AbstractLvaEntityService
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

    const LICENCE_STATUS_UNDER_CONSIDERATION = 'lsts_consideration';
    const LICENCE_STATUS_NOT_SUBMITTED = 'lsts_not_submitted';
    const LICENCE_STATUS_SUSPENDED = 'lsts_suspended';
    const LICENCE_STATUS_VALID = 'lsts_valid';
    const LICENCE_STATUS_CURTAILED = 'lsts_curtailed';
    const LICENCE_STATUS_GRANTED = 'lsts_granted';

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
        'children' => array(
            'licenceType',
            'status'
        )
    );

    /**
     * Bundle to check whether the application belongs to the organisation
     *
     * @var array
     */
    private $doesBelongToOrgBundle = array(
        'children' => array(
            'organisation'
        )
    );

    /**
     * Header data bundle
     *
     * @var array
     */
    private $headerDataBundle = array(
        'children' => array(
            'organisation',
            'status',
            'goodsOrPsv'
        )
    );

    protected $addressesDataBundle = array(
        'children' => array(
            'correspondenceCd' => array(
                'children' => array(
                    'address' => array(
                        'children' => array(
                            'countryCode'
                        )
                    ),
                    'phoneContacts' => array(
                        'children' => array(
                            'phoneContactType'
                        )
                    )
                )
            ),
            'establishmentCd' => array(
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

    /**
     * Safety data bundle
     *
     * @var array
     */
    protected $safetyDataBundle = array(
        'children' => array(
            'tachographIns'
        )
    );

    protected $vehicleDataBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'children' => array(
                    'goodsDiscs',
                    'vehicle'
                )
            )
        )
    );

    protected $vehiclePsvDataBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'children' => array(
                    'vehicle' => array(
                        'children' => array(
                            'psvType'
                        )
                    )
                )
            )
        )
    );

    protected $vehiclesTotalBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'criteria' => array(
                    'removalDate' => 'NULL'
                )
            )
        )
    );

    /**
     * Application traffic area bundle
     *
     * @var array
     */
    protected $trafficAreaBundle = array(
        'children' => array(
            'trafficArea'
        )
    );

    protected $licenceNoGenBundle = array(
        'children' => array(
            'trafficArea',
            'applications' => array(
                'criteria' => array(
                    'isVariation' => false
                ),
                'children' => array(
                    'goodsOrPsv'
                )
            )
        )
    );

    protected $psvDiscsBundle = array(
        'children' => array(
            'psvDiscs'
        )
    );

    protected $vehiclesPsvTotalBundle = array(
        'children' => array(
            'licenceVehicles' => array(
                'criteria' => array(
                    'removalDate' => 'NULL'
                ),
                'children' => array(
                    'vehicle' => array(
                        'children' => array(
                            'psvType'
                        )
                    )
                )
            )
        )
    );

    protected $categoryBundle = array(
        'children' => array(
            'goodsOrPsv'
        )
    );

    protected $organisationBundle = array(
        'children' => array(
            'organisation'
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
        return $this->get($id, $this->overviewBundle);
    }

    /**
     * Check whether the licence belongs to the organisation
     *
     * @param int $id
     * @param int $orgId
     * @return boolean
     */
    public function doesBelongToOrganisation($id, $orgId)
    {
        $data = $this->get($id, $this->doesBelongToOrgBundle);

        return (isset($data['organisation']['id']) && $data['organisation']['id'] == $orgId);
    }

    /**
     * Get data for header
     *
     * @param int $id
     * @return array
     */
    public function getHeaderParams($id)
    {
        return $this->get($id, $this->headerDataBundle);
    }

    /**
     * Get addresses data
     *
     * @param int $id
     * @return array
     */
    public function getAddressesData($id)
    {
        return $this->get($id, $this->addressesDataBundle);
    }

    /**
     * Get safety data
     *
     * @param int $id
     * @return array
     */
    public function getSafetyData($id)
    {
        return $this->get($id, $this->safetyDataBundle);
    }

    public function getVehiclesData($id)
    {
        return $this->get($id, $this->vehicleDataBundle)['licenceVehicles'];
    }

    public function getVehiclesPsvData($id)
    {
        return $this->get($id, $this->vehiclePsvDataBundle)['licenceVehicles'];
    }

    public function getCurrentVrms($id)
    {
        return $this->getServiceLocator()
            ->get('Entity\LicenceVehicle')
            ->getCurrentVrmsForLicence($id);
    }

    public function getVehiclesTotal($id)
    {
        $data = $this->get($id, $this->vehiclesTotalBundle);

        return count($data['licenceVehicles']);
    }

    public function getVehiclesPsvTotal($id, $type)
    {
        $data = $this->get($id, $this->vehiclesPsvTotalBundle);

        $count = 0;

        foreach ($data['licenceVehicles'] as $vehicle) {
            if (isset($vehicle['vehicle']['psvType']['id']) && $vehicle['vehicle']['psvType']['id'] === $type) {
                $count++;
            }
        }

        return $count;
    }

    public function getTotalAuths($id)
    {
        return $this->get($id);
    }

    public function getPsvDiscsRequestData($id)
    {
        return $this->get($id, $this->psvDiscsBundle);
    }

    public function getPsvDiscs($id)
    {
        return $this->get($id, $this->psvDiscsBundle)['psvDiscs'];
    }

    /**
     * Get traffic area for licence
     *
     * @param int $id
     * @return string
     */
    public function getTrafficArea($id)
    {
        $licence = $this->get($id, $this->trafficAreaBundle);

        if (isset($licence['trafficArea'])) {
            return $licence['trafficArea'];
        }

        return null;
    }

    /**
     * Set traffic area to application's licence based on area id
     *
     * @param int $licenceId
     * @param int $trafficAreaId
     */
    public function setTrafficArea($licenceId, $trafficAreaId = null)
    {
        $this->forceUpdate($licenceId, array('trafficArea' => $trafficAreaId));

        if ($trafficAreaId) {
            $this->generateLicence($licenceId);
        }
    }

    /**
     * Generates new licences or updates existing one and saves it to licence entity
     *
     * @param string $licenceId
     * @return string|bool
     */
    protected function generateLicence($licenceId)
    {
        $licence = $this->get($licenceId, $this->licenceNoGenBundle);

        if (!isset($licence['applications'][0]['goodsOrPsv']['id']) || !isset($licence['trafficArea']['id'])) {
            return;
        }

        $licenceCat = $licence['applications'][0]['goodsOrPsv']['id'];

        $saveData = array(
            'id' => $licence['id'],
            'version' => $licence['version']
        );

        if (empty($licence['licNo'])) {

            $licenceGen = $this->getServiceLocator()->get('Entity\LicenceNoGen')->save(array('licence' => $licenceId));

            if (!isset($licenceGen['id']) ) {
                throw new Exceptions\UnexpectedResponseException('Error generating licence');
            }

            $saveData['licNo'] = sprintf(
                '%s%s%s',
                $licenceCat === self::LICENCE_CATEGORY_PSV ? 'P' : 'O',
                $licence['trafficArea']['id'],
                $licenceGen['id']
            );

            $this->save($saveData);

            return;
        }

        if (substr($licence['licNo'], 1, 1) != $licence['trafficArea']['id']) {
            $saveData['licNo'] = sprintf(
                '%s%s%s',
                substr($licence['licNo'], 0, 1),
                $licence['trafficArea']['id'],
                substr($licence['licNo'], 2)
            );

            $this->save($saveData);
        }
    }

    public function findByIdentifier($identifier)
    {
        $result = $this->get(['licNo' => $identifier]);
        if ($result['Count'] === 0) {
            return false;
        }
        return $result['Results'][0];
    }

    public function getVariationData($id)
    {
        $data = $this->get($id, $this->overviewBundle);

        $keys = [
            'totAuthTrailers',
            'totAuthVehicles',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        ];

        $variationData = array_intersect_key($data, array_flip($keys));

        $variationData['licenceType'] = $data['licenceType']['id'];

        return $variationData;
    }

    public function getOrganisation($licenceId)
    {
        $response = $this->get($licenceId, $this->organisationBundle);

        return $response['organisation'];
    }
}
