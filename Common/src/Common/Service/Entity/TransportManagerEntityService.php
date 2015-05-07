<?php

/**
 * Transport Manager Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Transport Manager Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TransportManager';

    const TRANSPORT_MANAGER_STATUS_CURRENT = 'tm_s_cur';
    const TRANSPORT_MANAGER_STATUS_ACTIVE = 'tm_st_act';
    const TRANSPORT_MANAGER_STATUS_DISABLED = 'tm_st_disa';
    const TRANSPORT_MANAGER_TYPE_EXTERNAL = 'tm_t_e';
    const TRANSPORT_MANAGER_TYPE_BOTH = 'tm_t_b';

    protected $tmDetailsBundle = [
        'children' => [
            'homeCd' => [
                'children' => [
                    'person' => [
                        'children' => ['title']
                    ],
                    'address',
                    'contactType'
                ]
            ],
            'workCd' => [
                'children' => [
                    'address',
                    'contactType'
                ]
            ],
            'tmType',
            'tmStatus'
        ]
    ];

    /**
     * Document Bundle
     *
     * @var array
     */
    protected $documentBundle = array(
        'children' => array(
            'documents' => array(
                'children' => array(
                    'category',
                    'subCategory'
                )
            )
        )
    );

    /**
     * Markers Bundle
     *
     * @var array
     */
    protected $markersBundle = [
        'children' => [
            'qualifications' => [
                'children' => [
                    'qualificationType'
                ]
            ],
            'tmApplications' => [
                'children' => [
                    'application' => [
                        'children' => [
                            'status',
                            'licenceType',
                            'licence' => [
                                'children' => [
                                    'organisation'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'tmLicences' => [
                'children' => [
                    'licence' => [
                        'children' => [
                            'status',
                            'licenceType',
                            'organisation'
                        ]
                    ]
                ]
            ],
            'tmType',
            'homeCd' => [
                'children' => [
                    'person'
                ]
            ]
        ]
    ];

    /**
     * Get transport manager details
     *
     * @param int $id
     */
    public function getTmDetails($id)
    {
        return $this->get($id, $this->tmDetailsBundle);
    }

    /**
     * Find by id
     *
     * @param int $id
     * @return array
     */
    public function findByIdentifier($identifier)
    {
        return $this->get($identifier);
    }

    /**
     * Get transport manager documents
     *
     * @param int $tmId
     * @param int $secondaryId
     * @param string $type
     * @param int $categoryId
     * @param int $documentSubCategoryId
     * @return array
     */
    public function getDocuments($id, $secondaryId, $type, $categoryId, $documentSubCategoryId)
    {
        $documentBundle = $this->documentBundle;

        $documentBundle['children']['documents']['criteria'] = array(
            'category'    => $categoryId,
            'subCategory' => $documentSubCategoryId,
        );
        if ($secondaryId) {
            $documentBundle['children']['documents']['criteria'][$type] = $secondaryId;
        }

        $data = $this->get($id, $documentBundle);

        return $data['documents'];
    }

    /**
     * Get transport manager details for markers
     *
     * @param int $id
     */
    public function getTmForMarkers($id)
    {
        return $this->get($id, $this->markersBundle);
    }
}
