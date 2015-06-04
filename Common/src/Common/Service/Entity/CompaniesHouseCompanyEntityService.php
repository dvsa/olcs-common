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
        // cascade persist is broken following backend refactor, do it manually for now
        $officers = isset($data['officers']) ? $data['officers'] : null;
        unset($data['officers']);

        $result = $this->save($data);

        if (is_array($officers)) {
            array_walk(
                $officers,
                function (&$item) use ($result) {
                    $item['companiesHouseCompany'] = $result['id'];
                }
            );
            $this->getServiceLocator()->get('Entity\CompaniesHouseOfficer')
                ->multiCreate($officers);
        }

        return $result;
    }
}
