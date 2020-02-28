<?php

/**
 * Inspection Request Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Inspection Request Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InspectionRequestEntityService extends AbstractLvaEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'InspectionRequest';

    /**
     * Get inspection request list
     *
     * @param array $query
     * @param int $licenceId
     * @return array
     */
    public function getInspectionRequestList($query, $licenceId)
    {
        $listBundle = [
            'children' => [
                'reportType',
                'resultType',
                'application',
                'licence' => [
                    'criteria' => [
                        'id' => $licenceId,
                    ],
                    'required' => true,
                ],
            ]
        ];
        return $this->get($query, $listBundle);
    }

    /**
     * Get inspection request
     *
     * @param int $id
     * @return array
     */
    public function getInspectionRequest($id)
    {
        $bundle = [
            'children' => [
                'reportType',
                'requestType',
                'resultType',
                'application' => [
                    'children' => [
                        'licenceType',
                        'operatingCentres' => [
                            'children' => [
                                'operatingCentre' => [
                                    'children' => [
                                        'address'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'licence' => [
                    'children' => [
                        'enforcementArea',
                        'licenceType',
                        'organisation' => [
                            'children' => [
                                'tradingNames',
                                'licences',
                            ],
                        ],
                        'operatingCentres',
                        'correspondenceCd' => [
                            'children' => [
                                'address' => [],
                                'phoneContacts' => [
                                    'children' => [
                                        'phoneContactType',
                                    ],
                                ],
                            ],
                        ],
                        'tmLicences' => [
                            'children' => [
                                'transportManager' => [
                                    'children' => [
                                        'homeCd' => [
                                            'children' => [
                                                'person',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'operatingCentre' => [
                    'children' => [
                        'address'
                    ],
                ],
            ]
        ];
        return $this->get($id, $bundle);
    }
}
