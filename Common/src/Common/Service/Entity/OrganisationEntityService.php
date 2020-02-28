<?php

/**
 * Organisation Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\RefData;

/**
 * Organisation Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Organisation';

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

    /**
     * @NOTE This functionality has been migrated to the backend [Query\Organisation\BusinessDetails]
     *
     * @note need to check Olcs\Controller\Traits\OperatorControllerTrait->getViewWithOrganisation
     *
     * Get business details data
     *
     * @param type $id
     * @param int $licenceId
     */
    public function getBusinessDetailsData($id, $licenceId = null)
    {
        if ($licenceId) {
            $bundle = [
                'children' => [
                    'contactDetails' => [
                        'children' => [
                            'address',
                            'contactType'
                        ]
                    ],
                    'tradingNames' => [
                        'criteria' => [
                            'licence' => $licenceId
                        ]
                    ],
                    'type'
                ]
            ];
        } else {
            $bundle = $this->businessDetailsBundle;
        }
        return $this->get($id, $bundle);
    }

    /*
     * @note need to find out if ScanEntityProcessingService migrated
     */
    public function findByIdentifier($identifier)
    {
        return $this->get($identifier);
    }

    /**
     * @note used in isMlh method
     *
     * @param int $id organisation id
     * @param array $licenceStatuses only return child licences matching
     *        these statuses
     * @return array
     */
    public function getLicencesByStatus($id, $licenceStatuses)
    {
        $bundle = $this->licencesBundle;
        $bundle['children']['licences']['criteria'] = [
            'status' => 'IN ' . json_encode($licenceStatuses)
        ];
        return $this->get($id, $bundle)['licences'];
    }

    /**
     * Determine is an organisation isMlh (has at least one valid licence)
     *
     * @param $id
     * @return bool
     */
    public function isMlh($id)
    {
        $licences = $this->getLicencesByStatus($id, [RefData::LICENCE_STATUS_VALID]);
        return (bool) count($licences);
    }
}
