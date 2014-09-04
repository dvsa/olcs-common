<?php

/**
 * Safety Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\VehicleSafety;

use Common\Controller\Traits\VehicleSafety\SafetySection;

/**
 * Safety Controller
 *
 * @IMPORTANT Alot of the methods and logic from this controller are now stored in SafetySection trait, as it is re-used
 *  in the licence section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SafetyController extends VehicleSafetyController
{
    use SafetySection;

    /**
     * Whether or not to hide internal form elements
     *
     * @var boolean
     */
    protected $hideInternalFormElements = true;

    /**
     * Data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'properties' => array(
            'id',
            'version',
            'safetyConfirmation',
            'isMaintenanceSuitable'
        ),
        'children' => array(
            'licence' => array(
                'properties' => array(
                    'id',
                    'version',
                    'safetyInsVehicles',
                    'safetyInsTrailers',
                    'safetyInsVaries',
                    'tachographInsName'
                ),
                'children' => array(
                    'tachographIns' => array(
                        'properties' => array('id')
                    ),
                    'workshops' => array(
                        'properties' => array(
                            'id',
                            'isExternal'
                        ),
                        'children' => array(
                            'contactDetails' => array(
                                'properties' => array(
                                    'fao'
                                ),
                                'children' => array(
                                    'address' => array(
                                        'properties' => array(
                                            'addressLine1',
                                            'addressLine2',
                                            'addressLine3',
                                            'addressLine4',
                                            'town',
                                            'postcode'
                                        ),
                                        'children' => array(
                                            'countryCode' => array(
                                                'properties' => array('id')
                                            )
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
     * Get the form table data
     *
     * @param int $id
     * @param string $table
     */
    protected function getFormTableData($id, $table)
    {
        $loadData = $this->load($id);

        $data = $loadData['licence']['workshops'];

        return $this->doGetFormTableData($data);
    }

    /**
     * Remove the trailer fields for PSV
     *
     * @param \Zend\Form\Fieldset $form
     * @return \Zend\Form\Fieldset
     */
    protected function alterForm($form)
    {
        return $this->doAlterForm($form, $this->hideInternalFormElements, $this->isPsv());
    }

    /**
     * Save the form data
     *
     * @param array $data
     * @param string $service
     */
    protected function save($data, $service = null)
    {
        $data = $this->formatSaveData($data);

        parent::save($data['licence'], 'Licence');

        parent::save($data['application'], 'Application');
    }

    /**
     * Load the data for the form
     *
     * @param arary $data
     * @return array
     */
    protected function processLoad($data)
    {
        return $this->doProcessLoad($data);
    }
}
