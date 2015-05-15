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
            'companyStatus',
            'country',
            'officers' => [
                'children' => [
                    'role',
                ],
            ],
        ],
    ];

    public function getByCompanyNumber($number)
    {
        $result = $this->get(['companyNumber' => $number], $bundle);
        return (isset($result['Results']) ? $result['Results'][0] : false);
    }

    public function getByCompanyNumberForCompare($number)
    {
        $data = $this->getByCompanyNumber($number);

        if ($data !== false) {
            // flatten refdata children
            $data['companyStatus'] = $data['companyStatus']['id'];
            $data['country'] = $data['country']['id'];

            // @TODO flatten officer refdata children with array_map/walk
        }

        return $data;
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
