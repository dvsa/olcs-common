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
    const REASON_STATUS_CHANGE  = 'company_status_change';
    const REASON_NAME_CHANGE    = 'company_name_change';
    const REASON_ADDRESS_CHANGE = 'company_address_change';
    const REASON_PEOPLE_CHANGE  = 'company_people_change';
    const REASON_INVALID_COMPANY_NUMBER = 'invalid_company_number';

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
