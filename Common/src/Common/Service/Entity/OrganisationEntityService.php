<?php

/**
 * Organisation Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Organisation Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationEntityService extends AbstractEntityService
{
    /**
     * Organisation type keys
     */
    const ORG_TYPE_PARTNERSHIP = 'org_t_p';
    const ORG_TYPE_OTHER = 'org_t_pa';
    const ORG_TYPE_REGISTERED_COMPANY = 'org_t_rc';
    const ORG_TYPE_LLP = 'org_t_llp';
    const ORG_TYPE_SOLE_TRADER = 'org_t_st';

    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Organisation';

    /**
     * Holds the organisation bundle
     *
     * @var array
     */
    private $organisationFromUserBundle = array(
        'children' => array(
            'organisation'
        )
    );

    /**
     * Holds the organisation type bundle
     *
     * @var array
     */
    private $typeBundle = array(
        'children' => array(
            'type'
        )
    );

    /**
     * Bundle to retrieve data to update completion status
     *
     * @var array
     */
    private $businessDetailsBundle = array(
        'children' => array(
            'contactDetails' => array(
                'children' => array(
                    'address',
                    'contactType'
                )
            ),
            'type',
            'tradingNames'
        )
    );

    /**
     * Holds the applications bundle
     *
     * @var array
     */
    private $applicationsBundle = array(
        'children' => array(
            'licences' => array(
                'children' => array(
                    'applications' => array(
                        'children' => array(
                            'status'
                        )
                    ),
                    'licenceType',
                    'status'
                )
            )
        )
    );

    /**
     * Holds the licences bundle
     *
     * @var array
     */
    private $licencesBundle = array(
        'children' => array(
            'licences' => array(
                'children' => array(
                    'licenceType',
                    'status',
                    'goodsOrPsv'
                )
            )
        )
    );

    public function getApplications($id)
    {
        return $this->get($id, $this->applicationsBundle);
    }

    /**
     * @param int $id organisation id
     * @param array $applicationStatuses only return child applications
     *        matching these statuses
     * @return array
     */
    public function getNewApplicationsByStatus($id, $applicationStatuses)
    {
        $applications = [];

        $bundle = $this->applicationsBundle;
        $bundle['children']['licences']['children']['applications']['criteria'] = [
            'status' => 'IN ["'.implode('","', $applicationStatuses).'"]',
            'isVariation' => false,
        ];

        $data = $this->get($id, $bundle);
        foreach ($data['licences'] as $licence) {
            $applications = array_merge($applications, $licence['applications']);
        }

        return $applications;
    }

    /**
     * Get the organisation for the given user
     *
     * @param int $userId
     */
    public function getForUser($userId)
    {
        $organisation = $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('OrganisationUser', 'GET', ['user' => $userId], $this->organisationFromUserBundle);

        if ($organisation['Count'] < 1) {
            throw new Exceptions\UnexpectedResponseException('Organisation not found');
        }

        return $organisation['Results'][0]['organisation'];
    }

    /**
     * Get type of organisation
     *
     * @param int $id
     * @return array
     */
    public function getType($id)
    {
        return $this->get($id, $this->typeBundle);
    }

    /**
     * Get business details data
     *
     * @param type $id
     */
    public function getBusinessDetailsData($id)
    {
        return $this->get($id, $this->businessDetailsBundle);
    }

    public function findByIdentifier($identifier)
    {
        return $this->get($identifier);
    }

    public function hasInForceLicences($id)
    {
        $licences = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getInForceForOrganisation($id);

        return $licences['Count'] > 0;
    }

    public function hasChangedTradingNames($id, $tradingNames)
    {
        $data = $this->getBusinessDetailsData($id);

        $map = function ($v) {
            return $v['name'];
        };

        $existing = array_map($map, $data['tradingNames']);
        $updated  = array_map($map, $tradingNames);

        $diff = array_diff($updated, $existing);

        return count($existing) !== count($updated) || !empty($diff);
    }

    public function hasChangedRegisteredAddress($id, $address)
    {
        $data = $this->getBusinessDetailsData($id);

        $diff = $this->compareKeys(
            $data['contactDetails']['address'],
            $address,
            [
                'addressLine1', 'addressLine2',
                'addressLine3', 'addressLine4',
                'postcode', 'town',
            ]
        );

        return !empty($diff);
    }

    public function hasChangedNatureOfBusiness($id, $updated)
    {
        $existing = $this->getServiceLocator()
            ->get('Entity\OrganisationNatureOfBusiness')
            ->getAllForOrganisationForSelect($id);

        $diff = array_diff($updated, $existing);

        return count($existing) !== count($updated) || !empty($diff);
    }

    public function hasChangedSubsidiaryCompany($id, $company)
    {
        $existing = $this->getServiceLocator()
            ->get('Entity\CompanySubsidiary')
            ->getById($id);

        $diff = $this->compareKeys(
            $existing,
            $company,
            [
                'companyNo',
                'name'
            ]
        );

        return !empty($diff);
    }

    private function compareKeys($from, $to, $keys = [])
    {
        $keys = array_flip($keys);
        $from = array_intersect_key($from, $keys);
        $to   = array_intersect_key($to, $keys);

        return array_diff_assoc($to, $from);
    }

    /**
     * @param int $id organisation id
     * @param array $licenceStatuses only return child licences matching
     *        these statuses
     * @return array
     */
    public function getLicencesByStatus($id, $licenceStatuses)
    {
        $bundle = $this->licencesBundle;
        $bundle['children']['licences']['criteria'] = [
            'status' => 'IN ["'.implode('","', $licenceStatuses).'"]'
        ];
        return $this->get($id, $bundle)['licences'];
    }
}
