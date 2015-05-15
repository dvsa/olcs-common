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

    protected $entity = 'CompaniesHouseAlert';

    public function saveNew($data)
    {
        $meta = [
            '_OPTIONS_' => array(
                'cascade' => array(
                    'list' => array(
                        'reasons' => array(
                            'entity' => 'companiesHouseAlertReason',
                            'parent' => 'companiesHouseAlert',
                        )
                    )
                )
            )
        ];

        return $this->save(array_merge($meta, $data));
    }
}
