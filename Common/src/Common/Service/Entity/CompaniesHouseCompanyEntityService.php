<?php

/**
 * Companies House Company Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Entity;

/**
 * Companies House Company Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompaniesHouseCompanyEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'CompaniesHouseCompany';

    protected $bundle = [
        'children' => [
            'officers',
        ],
    ];

    public function getLatestByCompanyNumber($number)
    {
        $query = [
            'companyNumber' => $number,
            'sort' => 'createdOn',
            'order' => 'DESC',
            'limit' => 1,
        ];
        $result = $this->get($query, $this->bundle);
        return (isset($result['Results'][0]) ? $result['Results'][0] : false);
    }

    public function saveNew($data)
    {
        $meta = [
            '_OPTIONS_' => array(
                'cascade' => array(
                    'list' => array(
                        'officers' => array(
                            'entity' => 'companiesHouseOfficer',
                            'parent' => 'company',
                        )
                    )
                )
            )
        ];

        return $this->save(array_merge($meta, $data));
    }
}
