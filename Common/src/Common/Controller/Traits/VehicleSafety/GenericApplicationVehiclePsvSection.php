<?php

/**
 * Generic Application Vehicle PSV Section Trait
 *
 * Internal/External - Application - VehiclePsv Section
 *
 * @NOTE Includes shared logic between the APPLICATION/vehiclePSV, both internally and externally
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

/**
 * Generic Application Vehicle PSV Section Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericApplicationVehiclePsvSection
{
    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $sharedDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'hasEnteredReg'
        ),
        'children' => array(
            'licence' => array(
                'properties' => null,
                'children' => array(
                    'licenceVehicles' => array(
                        'properties' => array(
                            'id',
                            'specifiedDate',
                            'deletedDate'
                        ),
                        'children' => array(
                            'vehicle' => array(
                                'properties' => array(
                                    'vrm',
                                    'isNovelty',
                                ),
                                'children' => array(
                                    'psvType' => array(
                                        'properties' => array('id')
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    /**
     * Get the data bundle
     *
     * @return array
     */
    protected function getDataBundle()
    {
        return $this->sharedDataBundle;
    }

    /**
     * Return the form table data
     *
     * @return array
     */
    protected function getFormTableData($id, $table)
    {
        $data = $this->load($id);

        return $this->formatTableData($data, $table);
    }

    /**
     * Alter the form
     *
     * @param Form $form
     * @return Form
     */
    protected function alterForm($form)
    {
        return $this->doAlterForm($form);
    }

    /**
     * Save the vehicle
     *
     * @param array $data
     * @param string $service
     */
    protected function actionSave($data, $service = null)
    {
        $parts = explode('-', $this->getActionName());

        $action = array_pop($parts);

        return $this->doActionSave($data, $action);
    }
}
