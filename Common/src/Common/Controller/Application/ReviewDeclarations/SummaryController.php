<?php

/**
 * Summary Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
namespace Common\Controller\Application\ReviewDeclarations;

use Zend\View\Model\ViewModel;

/**
 * Summary Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class SummaryController extends ReviewDeclarationsController
{
    /**
     * don't ever attempt to validate forms on this page
     *
     * @var bool
     */
    protected $validateForm = false;

    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'properties' => 'ALL',
        'children' => array(
            'licence' => array(
                'children' => array(
                    'goodsOrPsv' => array(
                        'properties' => array('id')
                    ),
                    'licenceType' => array(
                        'properties' => array('id')
                    ),
                    'tachographIns' => array(
                        'properties' => array('id')
                    ),
                    'organisation' => array(
                        'children' => array(
                            'type' => array(
                            ),
                            'contactDetails' => array(
                                'children' => array(
                                    'contactType' => array(
                                        'properties' => array('id')
                                    ),
                                    'address' => array(
                                        'properties' => array(
                                            'id',
                                            'addressLine1',
                                            'addressLine2',
                                            'addressLine3',
                                            'addressLine4',
                                            'town',
                                            'postcode',
                                        ),
                                        'children' => array(
                                            'countryCode' => array(
                                                'properties' => array('id')
                                            )
                                        )
                                    ),
                                    'phoneContacts' => array(
                                    ),
                                ),
                            ),
                            'tradingNames' => array(
                            ),
                        ),
                    ),
                    'contactDetails' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'emailAddress'
                        ),
                        'children' => array(
                            'phoneContacts' => array(
                                'properties' => array(
                                    'id',
                                    'version',
                                    'phoneNumber'
                                ),
                                'children' => array(
                                    'phoneContactType' => array(
                                        'properties' => array(
                                            'id'
                                        )
                                    )
                                )
                            ),
                            'address' => array(
                                'properties' => array(
                                    'id',
                                    'version',
                                    'addressLine1',
                                    'addressLine2',
                                    'addressLine3',
                                    'addressLine4',
                                    'postcode',
                                    'town'
                                ),
                                'children' => array(
                                    'countryCode' => array(
                                        'properties' => array(
                                            'id'
                                        )
                                    )
                                )
                            ),
                            'contactType' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    )
                )
            ),
            'documents' => array()
        )
    );

    /**
     * We can't automagically map controllers to their tables, so
     * we have to maintain a manual mapping here
     *
     * @var array
     */
    private $tableConfigs = array(
        // controller => table config
        'YourBusiness/BusinessDetails' => array(
            'table' => 'application_your-business_business_details-subsidiaries'
        ),
        'YourBusiness/People' => array(
            'table' => 'application_your-business_people_in_form'
        ),
        'PreviousHistory/ConvictionsPenalties' => array(
            'table' => 'criminalconvictions'
        ),
        'PreviousHistory/LicenceHistory' => array(
            'table-licences-current' => 'previous_licences_current',
            'table-licences-applied' => 'previous_licences_applied',
            'table-licences-refused' => 'previous_licences_refused',
            'table-licences-revoked' => 'previous_licences_revoked',
            'table-licences-public-inquiry' => 'previous_licences_public_inquiry',
            'table-licences-disqualified' => 'previous_licences_disqualified',
            'table-licences-held' => 'previous_licences_held'
        ),
        'VehicleSafety/Safety' => array(
            'table' => 'safety-inspection-providers'
        ),
        'OperatingCentres/Authorisation' => array(
            'table' => 'authorisation_in_form'
        )
    );

    /**
     * We'll populate this later when calling generateSummary()
     *
     * @var array
     */
    private $summarySections = array();

    /**
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->generateSummary();
        return $this->renderSection();
    }

    /**
     * Alter the form
     *
     * @param Form $form
     * @return Form
     */
    protected function alterForm($form)
    {
        $data = $this->loadCurrent();

        foreach ($this->summarySections as $summarySection) {
            list($section, $subSection) = explode('/', $summarySection);

            // extract from our uber form which fieldsets are relevant
            // to this particular section
            $sectionFieldsets = $this->getSectionFieldsets(
                $form,
                $this->formatFormName('Application', $section, $subSection)
            );

            // naturally if we can't yet access this section make sure we hide
            // any fieldsets which relate to it
            if (!$this->isSectionAccessible($section, $subSection)) {
                foreach ($sectionFieldsets as $fieldset) {
                    $form->remove($fieldset);
                }
            } else {

                // if we're in here then check to see if the relevant controller wants
                // to make any extra form alterations based on the fact it is being
                // rendered out of context on the review page
                $controller = $this->getInvokable($summarySection, 'makeFormAlterations');
                if ($controller) {
                    $options = array(
                        // always let the controller know this is a review
                        'isReview'  => true,
                        'isPsv'     => $this->isPsv(),
                        // some forms only have one fieldset, so we pass the
                        // first through to be helpful...
                        'fieldset'  => $sectionFieldsets[0],
                        // ... but pass the rest through too, just in case
                        'fieldsets' => $sectionFieldsets,
                        // finally, at this stage some controllers may alter based on
                        // data available (or not); so pass that through too
                        'data'      => $data
                    );
                    $form = $controller::makeFormAlterations($form, $this, $options);
                }
            }
        }

        return $form;
    }

    /**
     * Render the section form
     *
     * @return Response
     */
    public function simpleAction()
    {
        $this->isAction = false;

        $this->setRenderNavigation(false);
        $this->setLayout('layout/simple');

        $this->generateSummary();
        $layout = $this->renderSection();

        if ($layout instanceof ViewModel) {
            $layout->setTerminal(true);
        }

        return $layout;
    }

    /**
     * Placeholder save method
     *
     * @param array $data
     * @param string $service
     */
    protected function save($data, $service = null)
    {
    }

    /**
     * Process load
     *
     * @param array $loadData
     */
    protected function processLoad($loadData)
    {
        $translator = $this->getServiceLocator()->get('translator');

        /*
         * Flatten out the contacts so they're in a mapped array as used
         * by the Your Business -> Addresses fieldset.
         */
        $contactList=$loadData['licence']['organisation']['contactDetails'];
        $indexedContactList=array();
        foreach ($contactList as $contactEntry) {
            $indexedContactList[$contactEntry['contactType']['id']]=$contactEntry['address'];
        }
        $indexedContactList['ct_corr']=$loadData['licence']['contactDetails'];

        // Flatten out the phone contacts from the only licence contact
        $phoneList=$loadData['licence']['contactDetails'][0]['phoneContacts'];
        $indexedPhoneList=array();
        foreach ($phoneList as $phoneEntry) {
            $indexedPhoneList[$phoneEntry['phoneContactType']['id']]=$phoneEntry['phoneNumber'];
        }

        // Trading names requires specific formatting
        $flatTradingNamesList=array();
        if ( isset($loadData['licence']['organisation']['tradingNames']) ) {
            $tradingNamesList=$loadData['licence']['organisation']['tradingNames'];
            foreach ($tradingNamesList as $tradingName) {
                $flatTradingNamesList[]=$tradingName['name'];
            }
        }

        $data = array(
            /**
             * Type of Licence
             */
            'application_type-of-licence_operator-location-1' => array(
                'niFlag' => $loadData['licence']['niFlag']
            ),
            'application_type-of-licence_operator-type-1' => array(
                'goodsOrPsv' => $loadData['licence']['goodsOrPsv']['id']
            ),
            'application_type-of-licence_licence-type-1' => array(
                'licenceType' => $loadData['licence']['licenceType']['id']
            ),

            /**
             * Your Business
             */
            'application_your-business_business-type-1' => array(
                'type' => $loadData['licence']['organisation']['type']['id']
            ),
            'application_your-business_business-details-1' => array(
                'companyNumber' => array(
                    'company_number' => $loadData['licence']['organisation']['companyOrLlpNo']
                ),
                'tradingNamesReview' => implode(PHP_EOL, $flatTradingNamesList),
                'name' => $loadData['licence']['organisation']['name']
            ),
            'application_your-business_business-details-2' => array(
            ),

            // Correspondence Address
            'application_your-business_addresses-2' => $this->mapAddressFields(
                'ct_oc',
                $indexedContactList
            ),

            // Contact Details
            'application_your-business_addresses-3' => array(
                'phone_business' => (isset($indexedPhoneList['phone_t_tel'])?$indexedPhoneList['phone_t_tel']:''),
                'phone_home' => (isset($indexedPhoneList['phone_t_home'])?$indexedPhoneList['phone_t_home']:''),
                'phone_mobile' => (isset($indexedPhoneList['phone_t_mobile'])?$indexedPhoneList['phone_t_mobile']:''),
                'phone_fax' => (isset($indexedPhoneList['phone_t_fax'])?$indexedPhoneList['phone_t_fax']:''),
                'email' => $loadData['licence']['contactDetails'][0]['emailAddress']
            ),

            // Establishment Address
            'application_your-business_addresses-5' => $this->mapAddressFields(
                'ct_est',
                $indexedContactList
            ),

            // Registered Office Address
            'application_your-business_addresses-7' => $this->mapAddressFields(
                'ct_reg',
                $indexedContactList
            ),

            /**
             * OC&A
             */
            'application_operating-centres_authorisation-1' => array(),
            'application_operating-centres_authorisation-3' => $this->mapApplicationVariables(
                array(
                    'totAuthSmallVehicles',
                    'totAuthMediumVehicles',
                    'totAuthLargeVehicles',
                    'totCommunityLicences',
                    'totAuthVehicles',
                    'totAuthTrailers'
                ),
                $loadData
            ),

            /**
             * Previous History
             */
            'application_previous-history_financial-history-1' => $this->mapApplicationVariables(
                array(
                    'bankrupt',
                    'liquidation',
                    'receivership',
                    'administration',
                    'disqualified',
                    'insolvencyDetails',
                    'insolvencyConfirmation'
                ),
                $loadData
            ),
            // @NOTE licence history section not yet implemented so no data to map
            'application_previous-history_licence-history-1' => array(),
            'application_previous-history_convictions-penalties-1' => array(
                'prevConviction' => $loadData['prevConviction']
            ),
            // @NOTE application_previous-history_convictions-penalties-2 is table data
            'application_previous-history_convictions-penalties-3' => $this->mapApplicationVariables(
                array('convictionsConfirmation'),
                $loadData
            ),

            /**
             * Vehicles & Safety
             */
            'application_vehicle-safety_safety-1' => array(
                'safetyInsVehicles' => 'inspection_interval_vehicle.'.$loadData['licence']['safetyInsVehicles'],
                'safetyInsTrailers' => 'inspection_interval_trailer.'.$loadData['licence']['safetyInsTrailers'],
                'safetyInsVaries' => $loadData['licence']['safetyInsVaries'],
                'tachographIns' => isset($loadData['licence']['tachographIns']['id']) ?
                    $loadData['licence']['tachographIns']['id'] : '',
                'tachographInsName' => $loadData['licence']['tachographInsName'],
            ),

            // @NOTE application_vehicle-safety_safety-2 is table data
            'application_vehicle-safety_safety-2' => array(
                'workshops' => $loadData['licence']['workshops']
            ),

            'application_vehicle-safety_safety-3' => array(
                'isMaintenanceSuitable' => $loadData['isMaintenanceSuitable'],
                'safetyConfirmation' => $loadData['safetyConfirmation']
            ),
            /**
             * Vehicle Undertakings
             */
            'application_vehicle-safety_undertakings-2' => array(
                'psvOperateSmallVhl' => $loadData['psvOperateSmallVhl'],
                'psvSmallVhlConfirmation' => ($loadData['psvSmallVhlConfirmation']=='Y'?1:0),
                'psvSmallVhlNotes' => $loadData['psvSmallVhlNotes'],
                'psvSmallVhlUndertakings' =>
                    $translator->translate(
                        'application_vehicle-safety_undertakings.smallVehiclesUndertakings.text'
                    ),
                'psvSmallVhlScotland' =>
                    $translator->translate(
                        'application_vehicle-safety_undertakings.smallVehiclesUndertakingsScotland.text'
                    )
            ),

            'application_vehicle-safety_undertakings-3' => array(
                'psvNoSmallVhlConfirmation' => ($loadData['psvNoSmallVhlConfirmation']=='Y')

            ),

            'application_vehicle-safety_undertakings-4' => array(
                'psvLimousines' => ($loadData['psvLimousines']?'Y':'N'),
                'psvNoLimousineConfirmation' => $loadData['psvNoLimousineConfirmation'],
                'psvOnlyLimousinesConfirmation' => $loadData['psvOnlyLimousinesConfirmation'],
            )
        );

        return $data;
    }

    /**
     * Helper to map the address fields
     */
    protected function mapAddressFields($contactType, $data)
    {
        // If the contact type doesn't exist, return a blank
        if ( ! isset($data[$contactType]) ) {
            return array();
        }

        return array(
             'addressLine1' => $data[$contactType]['addressLine1'],
             'addressLine2' => $data[$contactType]['addressLine2'],
             'addressLine3' => $data[$contactType]['addressLine3'],
             'addressLine4' => $data[$contactType]['addressLine4'],
             'town' => $data[$contactType]['town'],
             'postcode' => $data[$contactType]['postcode'],
             'country' => $data[$contactType]['countryCode']['id']
         );
    }

    /**
     * Simple helper to map a subset of an input array
     * into an output array, as long as they exist
     *
     * @param array $map
     * @param array $data
     *
     * @return array
     */
    protected function mapApplicationVariables($map, $data)
    {
        $final = array();

        foreach ($map as $entry) {
            if (isset($data[$entry])) {
                $final[$entry] = $data[$entry];
            }
        }

        return $final;
    }

    /**
     * Override the abstract method to get form data on a per-fieldset
     * basis, deferring to the relevant controller's data method to
     * fulfil the request
     */
    protected function getFormTableData($id, $table)
    {
        // this will contain the actual table config to load
        $table = $this->formTables[$table];

        // we can use this value to map back to the controller which
        // knows how to populate its data
        // (this needs to know about subsections)
        $section=false;
        foreach ($this->tableConfigs as $controllerName => $controllerTableConfig) {
            $sectionSearch = array_search(
                $table,
                $controllerTableConfig
            );

            if ( $sectionSearch ) {
                $section=$controllerName;
            }
        }

        $controller = $this->getInvokable($section, 'getSummaryTableData');

        if ($controller) {
            return $controller::getSummaryTableData($id, $this, $table);
        }
    }

    /**
     * Helper method to try and automagically generate as much as we can based
     * on the form config which drives this page. This exists to stop us having to
     * repeat a lot of code, albeit in slightly different ways, between the
     * form config and this controller
     */
    private function generateSummary()
    {
        $this->summarySections = [];
        $this->formTables      = [];

        $fieldsets = $this->getForm($this->getFormName())->getFieldsets();

        foreach ($fieldsets as $fieldset) {

            $name = $fieldset->getName();

            if (preg_match("/application_([\w-]+)_([\w-]+)-\d+/", $name, $matches)) {
                $section        = $this->dashToCamel($matches[1]);
                $subSection     = $this->dashToCamel($matches[2]);
                $summarySection = $section . '/' . $subSection;

                $this->summarySections[] = $summarySection;

                if ($fieldset->has('table')) {
                    $originalName = $fieldset->getAttribute('unmappedName');
                    $this->formTables[$name] = $this->tableConfigs[$summarySection][$originalName];
                }
            }
        }

        $this->summarySections=array_unique($this->summarySections);
    }

    /**
     * Determine whether a) a controller exists and b) whether it has an
     * invokable method we want to call
     *
     * @param string $section
     * @param string $method
     *
     * @return Controller
     */
    private function getInvokable($section, $method)
    {
        list($section, $subSection) = explode('/', $section);
        $controller = '\Common\Controller\Application\\' . $section . '\\' . $subSection . 'Controller';
        if (is_callable(array($controller, $method))) {
            return $controller;
        }
        return null;
    }

    /**
     * Retrieve all the fieldsets within a form which relate to a given section
     *
     * @param Form $form
     * @param string $fieldsetName
     *
     * @return array
     */
    private function getSectionFieldsets($form, $fieldsetName)
    {
        $fieldsets = array_keys($form->getFieldsets());
        $sectionFieldsets = [];

        foreach ($fieldsets as $fieldset) {
            if (strpos($fieldset, $fieldsetName) !== false) {
                $sectionFieldsets[] = $fieldset;
            }
        }
        return $sectionFieldsets;
    }
}
