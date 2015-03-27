<?php

/**
 * Cases Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Cases Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CasesEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Cases';

    private $identifierBundle = [
        'children' => [
            'licence',
            'transportManager'
        ]
    ];

    private $openForLicenceBundle = [
        'children' => ['publicInquirys']
    ];

    private $tableBundle = [
        'children' => [
            'complaints' => [
                'criteria' => [
                    'isCompliance' => 0
                ],
                'children' => [
                    'complainantContactDetails' => [
                        'children' => [
                            'person'
                        ]
                    ],
                    'ocComplaints' => [
                        'children' => [
                            'operatingCentre' => [
                                'children' => ['address']
                            ]
                        ]
                    ],
                    'status'
                ]
            ]
        ]
    ];

    public function findByIdentifier($identifier)
    {
        // a case's identifier is also its primary key...
        return $this->get($identifier, $this->identifierBundle);
    }

    public function getOpenForLicence($licenceId)
    {
        $query = [
            'licence' => $licenceId,
            'closedDate' => 'NULL',
            'deletedDate' => 'NULL',
        ];

        $data = $this->getAll($query, $this->openForLicenceBundle);

        return $data['Results'];
    }

    /**
     * Get cases with complaints for an application
     *
     * @param int $applicationId Application Id
     *
     * @return array
     */
    public function getComplaintsForApplication($applicationId)
    {
        $query = [
            'application' => $applicationId,
        ];

        return $this->getAll($query, $this->tableBundle)['Results'];
    }

    /**
     * Get cases with complaints for a licence
     *
     * @param int $licenceId Licence Id
     *
     * @return array
     */
    public function getComplaintsForLicence($licenceId)
    {
        $query = [
            'licence' => $licenceId,
        ];

        return $this->getAll($query, $this->tableBundle)['Results'];
    }
}
