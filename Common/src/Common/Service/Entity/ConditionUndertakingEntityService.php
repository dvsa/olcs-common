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

    const TYPE_CONDITION = 'cdt_con';
    const TYPE_UNDERTAKING = 'cdt_und';

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

    protected $grantDataBundle = array(
        'children' => array(
            'case',
            'attachedTo',
            'conditionType',
            'operatingCentre',
            'addedVia',
            'licConditionVariation'
        )
    );

    protected $variationDataBundle = array(
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
            'application',
            'licence',
            'licConditionVariation',
            'variationRecords' => array(
                'criteria' => array()
            )
        )
    );

    public function getCondition($id)
    {
        return $this->get($id, $this->dataBundle);
    }

    public function getConditionForVariation($id, $parentId)
    {
        $bundle = $this->variationDataBundle;

        $bundle['children']['variationRecords']['criteria']['application'] = $parentId;

        return $this->get($id, $bundle);
    }

    public function getForApplication($applicationId)
    {
        return $this->getAll(['application' => $applicationId], $this->dataBundle)['Results'];
    }

    public function getGrantData($applicationId)
    {
        return $this->getAll(['application' => $applicationId], $this->grantDataBundle)['Results'];
    }

    public function getForVariation($applicationId)
    {
        $licenceId = $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($applicationId);

        $bundle = $this->variationDataBundle;
        $bundle['children']['variationRecords']['criteria']['application'] = $applicationId;

        $query = [
            [
                'application' => $applicationId,
                'licence' => $licenceId
            ]
        ];

        return $this->getAll($query, $bundle)['Results'];
    }

    public function getForLicence($licenceId)
    {
        return $this->getAll(['licence' => $licenceId], $this->dataBundle)['Results'];
    }
}
