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
        $bundle = [
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

        return $this->getAll($query, $bundle)['Results'];
    }
}
