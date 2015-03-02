<?php

/**
 * Transport Manager Qualification Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Transport Manager Qualification Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmQualificationEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TmQualification';

    protected $dataBundle = [
        'children' => [
            'qualificationType',
            'countryCode',
            'transportManager'
        ]
    ];

    /**
     * Get transport manager qualifiactions
     *
     * @param int $id
     * @return array
     */
    public function getQualificationsForTm($id)
    {
        $query = [
            'transportManager' => $id
        ];
        $results = $this->get($query, $this->dataBundle);

        usort(
            $results['Results'], function ($a, $b) {
                if ($a['qualificationType']['displayOrder'] == $b['qualificationType']['displayOrder']) {
                     return 0;
                }
                return ($a['qualificationType']['displayOrder'] > $b['qualificationType']['displayOrder']) ? +1 : -1;
            }
        );

        return $results;
    }

    /**
     * Get qualification
     *
     * @param int $id
     * @return array
     */
    public function getQualification($id)
    {
        return $this->get($id, $this->dataBundle);
    }
}
