<?php

/**
 * Safety Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\VehicleSafety;

use Common\Controller\Traits\SafetySection;

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
                )
            )
        )
    );

    /**
     * Get the form table data - in this case simply invoke the same logic
     * as when rendered on a summary page, but provide the controller for context
     *
     * @return array
     */
    protected function getFormTableData($id, $table)
    {
        return static::getSummaryTableData($id, $this, $table);
    }

    /**
     * Get the form table data
     *
     * @param int $id
     * @param string $table
     */
    public static function getSummaryTableData($applicationId, $context, $tableName)
    {
        $dataBundle = array(
            'properties' => array(
                'id',
                'version'
            ),
            'children' => array(
                'licence' => array(
                    'children' => array(
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

        $loadData = $context->makeRestCall(
            'Application',
            'GET',
            array('id' => $applicationId),
            $dataBundle
        );

        $data = $loadData['licence']['workshops'];

        // Translate contact details to a flat structure
        $translatedData=Array();
        foreach($data as $row) {
            $translatedRow=Array(
                'isExternal' => $row['isExternal'],
                'id' => $row['id'],
                'fao' => $row['contactDetails']['fao'],
                'addressLine1' => $row['contactDetails']['address']['addressLine1'],
                'addressLine2' => $row['contactDetails']['address']['addressLine2'],
                'addressLine3' => $row['contactDetails']['address']['addressLine3'],
                'addressLine4' => $row['contactDetails']['address']['addressLine4'],
                'town' => $row['contactDetails']['address']['town'],
                'postcode' => $row['contactDetails']['address']['postcode'],
                'countryCode' => array('id' => $row['contactDetails']['address']['countryCode']['id'])
            );
            $translatedData[]=$translatedRow;
        }

        return $translatedData;
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
