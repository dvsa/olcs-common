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

    const TRANSPORT_MANAGER_STATUS_ACTIVE = 'tm_st_A';

    const TRANSPORT_MANAGER_STATUS_DISABLED = 'tm_st_D';

    protected $tmDetailsBundle = [
        'children' => [
            'contactDetails' => [
                'children' => [
                    'person',
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
     * @param int $appId
     * @param int $categoryId
     * @param int $documentSubCategoryId
     * @return array
     */
    public function getDocuments($id, $appId, $categoryId, $documentSubCategoryId)
    {
        $documentBundle = $this->documentBundle;

        $documentBundle['children']['documents']['criteria'] = array(
            'category'    => $categoryId,
            'subCategory' => $documentSubCategoryId,
            'application' => $appId
        );

        $data = $this->get($id, $documentBundle);

        return $data['documents'];
    }
}
