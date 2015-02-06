<?php

/**
 * Condition Undertaking Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Condition Undertaking Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ConditionUndertakingEntityService extends AbstractEntityService
{
    const ATTACHED_TO_LICENCE = 'cat_lic';
    const ATTACHED_TO_OPERATING_CENTRE = 'cat_oc';

    const ADDED_VIA_CASE = 'cav_case';
    const ADDED_VIA_LICENCE = 'cav_lic';
    const ADDED_VIA_APPLICATION = 'cav_app';

    protected $entity = 'ConditionUndertaking';

    protected $dataBundle = array(
        'children' => array(
            'case',
            'attachedTo',
            'conditionType',
            'operatingCentre' => array(
                'children' => array(
                    'address' => array(
                        'children' => array(
                            'countryCode'
                        )
                    )
                )
            ),
            'addedVia',
        )
    );

    public function getCondition($id)
    {
        return $this->get($id, $this->dataBundle);
    }

    public function getForApplication($applicationId)
    {
        return $this->getAll(['application' => $applicationId], $this->dataBundle)['Results'];
    }
}
