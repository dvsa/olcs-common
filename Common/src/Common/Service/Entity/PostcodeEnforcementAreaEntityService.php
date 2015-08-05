<?php

/**
 * PostcodeEnforcementArea Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Entity;

/**
 * PostcodeEnforcementArea Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PostcodeEnforcementAreaEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'PostcodeEnforcementArea';

    private $bundle = [
        'children' => [
            'enforcementArea',
        ],
    ];

    /**
     * @param string $prefix e.g. 'LS9' or 'LS9 6'
     * @return array
     * @todo maybe remove
     */
    public function getEnforcementAreaByPostcodePrefix($prefix)
    {
        $query = [
            'postcodeId' => $prefix,
        ];

        $data = $this->get($query, $this->bundle);

        if ($data['Count'] == 0) {
            return null;
        }

        return $data['Results'][0]['enforcementArea'];
    }
}
