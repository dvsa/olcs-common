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

    public function getLicences($id)
    {
        return $this->get($id, $this->licencesBundle)['licences'];
    }

    /**
     * @param int $id organisation id
     * @param string|array $applicationStatus only return child applications
     *        matching this/these status(es)
     * @return array
     */
    public function getNewApplicationsByStatus($id, $applicationStatus)
    {
        $applications = [];

        if (is_array($applicationStatus)) {
            $statusCriteria = 'IN ["'.implode('","', $applicationStatus).'"]';
        } else {
            $statusCriteria = $applicationStatus;
        }

        $bundle = $this->applicationsBundle;
        $bundle['children']['licences']['children']['applications']['criteria'] = [
            'status' => $statusCriteria
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

    /**
     * @param int $id organisation id
     * @param string|array $licenceStatus only return child licences matching
     *        this/these status(es)
     * @return array
     */
    public function getLicencesByStatus($id, $licenceStatus)
    {
        if (is_array($licenceStatus)) {
            $statusCriteria = 'IN ["'.implode('","', $licenceStatus).'"]';
        } else {
            $statusCriteria = $licenceStatus;
        }

        $bundle = $this->licencesBundle;
        $bundle['children']['licences']['criteria'] = [
            'status' => $statusCriteria
        ];

        return $this->get($id, $bundle)['licences'];
    }
}
