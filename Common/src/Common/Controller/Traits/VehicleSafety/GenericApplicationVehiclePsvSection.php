<?php

/**
 * Generic Application Vehicle PSV Section Trait
 *
 * Internal/External - Application - VehiclePsv Section
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

    protected $totalNumberOfVehiclesBundle = array(
        'properties' => array(),
        'children' => array(
            'licence' => array(
                'properties' => array(),
                'children' => array(
                    'licenceVehicles' => array(
                        'properties' => array(),
                        'children' => array(
                            'vehicle' => array(
                                'properties' => array(
                                    'id'
                                ),
                                'children' => array(
                                    'psvType' => array(
                                        'properties' => array(
                                            'id'
                                        )
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
        $action = $this->getActionFromFullActionName();

        return $this->doActionSave($data, $action);
    }

    /**
     * Get total number of vehicles
     *
     * @return int
     */
    protected function getTotalNumberOfVehicles($type)
    {
        $psvType = $this->getPsvTypeFromType($type);

        $data = $this->makeRestCall(
            'Application',
            'GET',
            array('id' => $this->getIdentifier()),
            $this->totalNumberOfVehiclesBundle
        );

        $count = 0;

        foreach ($data['licence']['licenceVehicles'] as $row) {
            if (isset($row['vehicle']['psvType']['id']) && $row['vehicle']['psvType']['id'] == $psvType) {
                $count++;
            }
        }

        return $count;
    }
}
