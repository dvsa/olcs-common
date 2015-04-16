<?php

namespace Common\Service\Entity;

/**
 * Opposition Entity Service
 *
 * @package Common\Service\Entity
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OppositionEntityService extends AbstractEntityService
{
    /**
     * The entity reference.
     *
     * @var string
     */
    protected $entity = 'Opposition';

    private $tableBundle = [
        'children' => [
            'case',
            'oppositionType',
            'opposer' => array(
                'children' => array(
                    'opposerType',
                    'contactDetails' => array(
                        'children' => array(
                            'person',
                        )
                    )
                )
            ),
            'grounds',
            'application',
        ]
    ];

    /**
     * Get opposition for an application
     *
     * @param int $applicationId Application ID
     *
     * @return array
     */
    public function getForApplication($applicationId)
    {
        $query = [
            'application' => $applicationId,
            'sort' => 'createdOn',
            'order' => 'DESC',
        ];
        return $this->getAll($query, $this->tableBundle)['Results'];
    }

    /**
     * Get opposition for a licence
     *
     * @param int $licenceId Licence ID
     *
     * @return array
     */
    public function getForLicence($licenceId)
    {
        $query = [
            'licence' => $licenceId,
            'sort' => 'createdOn',
            'order' => 'DESC',
        ];

        return $this->getAll($query, $this->tableBundle)['Results'];
    }
}
