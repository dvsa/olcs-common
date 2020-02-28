<?php

/**
 * Companies House Alert Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Entity;

/**
 * Companies House Alert Entity Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompaniesHouseAlertEntityService extends AbstractEntityService
{
    protected $entity = 'CompaniesHouseAlert';

    protected $listBundle = [
        'children' => [
            'reasons' => [
                'children' => [
                    'reasonType',
                ],
            ],
            'organisation',
        ],
    ];

    public function saveNew($data)
    {
        // cascade persist is broken following backend refactor, do it manually for now
        $reasons = isset($data['reasons']) ? $data['reasons'] : null;
        unset($data['reasons']);

        $result = $this->save($data);

        if (is_array($reasons)) {
            array_walk(
                $reasons,
                function (&$item) use ($result) {
                    $item['companiesHouseAlert'] = $result['id'];
                }
            );
            $this->getServiceLocator()->get('Entity\CompaniesHouseAlertReason')
                ->multiCreate($reasons);
        }

        return $result;
    }
}
