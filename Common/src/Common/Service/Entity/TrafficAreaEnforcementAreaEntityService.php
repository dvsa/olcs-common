<?php

/**
 * TrafficAreaEnforcementArea Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Entity;

/**
 * TrafficAreaEnforcementArea Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TrafficAreaEnforcementAreaEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TrafficAreaEnforcementArea';

    private $bundle = [
        'children' => [
            'enforcementArea'
        ],
    ];

    /**
     * Get Enforcement Area value options for a given traffic area to populate
     * a select element
     *
     * @return array
     */
    public function getValueOptions($trafficArea)
    {
        $query = [
            'trafficArea' => $trafficArea,
        ];

        $all = $this->getAll($query, $this->bundle);

        $results = $all['Results'];

        $valueOptions = [];
        foreach ($results as $value) {
            $valueOptions[$value['enforcementArea']['id']] = $value['enforcementArea']['name'];
        }
        asort($valueOptions);

        return $valueOptions;
    }
}
