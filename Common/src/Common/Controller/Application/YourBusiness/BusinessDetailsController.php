<?php

/**
 * BusinessDetails Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
namespace Common\Controller\Application\YourBusiness;

/**
 * BusinessDetails Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class BusinessDetailsController extends YourBusinessController
{
    /**
     * Section service
     *
     * @var string
     */
    protected $service = 'Application';

    /**
     * Section data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'children' => array(
            'licence' => array(
                'children' => array(
                    'organisation' => array(
                        'children' => array(
                            'type' => array(
                                'properties' => array(
                                    'id'
                                )
                            ),
                            'tradingNames' => array(
                                'properties' => array(
                                    'id',
                                    'name'
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    public static $applicationBundle = array(
        'children' => array(
            'licence' => array(
                'children' => array(
                    'organisation' => array(
                        'properties' => array(
                            'id',
                            'version'
                        ),
                        'children' => array(
                            'type' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    public static $subCompanyBundle = array(
        'properties' => array(
            'id',
            'version',
            'name',
            'companyNo'
        )
    );


    /**
     * Holds the sub action service
     *
     * @var string
     */
    protected $actionService = 'CompanySubsidiary';

    /**
     * Company table bundle
     */
    protected $actionBundle = array(
        'children' => array(
            'licence' => array(
                'children' => array(
                    'organisation' => array(
                        'children' => array(
                            'type' => array(
                                'properties' => array(
                                    'id'
                                )
                            ),
                            'tradingNames' => array(
                                'properties' => array(
                                    'id',
                                    'name'
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    /**
     * Holds the actionDataBundle
     *
     * @var array
     */
    protected $actionDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'name',
            'companyNo'
        )
    );

    /**
     * Form tables name
     *
     * @var string
     */
    protected $formTables = array(
        'table' => 'application_your-business_business_details-subsidiaries'
    );

    /**
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->renderSection();
    }

    /**
     * Save data
     *
     * @param array $data
     * @parem string $service
     */
    protected function save($data, $service = null)
    {
        if (isset($data['companyNumber'])) {
            // unfortunately the company number field is a complex one so can't
            // be mapped directly
            $data['companyOrLlpNo'] = $data['companyNumber']['company_number'];
        }

        if (isset($data['tradingNames'])) {

            $licence = $this->getLicenceData();
            $tradingNames = [];

            foreach ($data['tradingNames']['trading_name'] as $tradingName) {

                if (trim($tradingName['text']) !== '') {
                    $tradingNames[] = [
                        'name' => $tradingName['text']
                    ];
                }
            }

            $data['tradingNames'] = $tradingNames;

            $tradingNameData = array(
                'organisation' => $data['id'],
                'licence' => $licence['id'],
                'tradingNames' => $tradingNames
            );

            $this->makeRestCall('TradingNames', 'POST', $tradingNameData);
        }

        unset($data['type']);
        unset($data['edit_business_type']);
        unset($data['companyNumber']);
        unset($data['tradingNames']);

        // we shouldn't really need to do this; it's only
        // because our $service property is set to Application
        // so we can fetch tradingNames as a child value
        return parent::save($data, 'Organisation');
    }

    /**
     * Make form alterations
     *
     * This method enables the summary to apply the same form alterations. In this
     * case we ensure we manipulate the form based on whether the license is PSV or not
     *
     * @param Form $form
     * @param mixed $context
     * @param array $options
     *
     * @return $form
     */
    public static function makeFormAlterations($form, $context, $options = array())
    {
        $application = $context->makeRestCall(
            'Application',
            'GET',
            array('id' => $context->getIdentifier()),
            self::$applicationBundle
        );

        $organisation=$application['licence']['organisation'];

        // Need to enumerate the form fieldsets with their mapping, as we're
        // going to use old/new
        $fieldsetMap = array();
        if ( $options['isReview'] ) {
            foreach ($options['fieldsets'] as $fieldset) {
                $fieldsetMap[$form->get($fieldset)->getAttribute('unmappedName')] = $fieldset;
            }
        } else {
            $fieldsetMap = array(
                'data' => 'data',
                'table' => 'table'
            );
        }

        $fieldset = $form->get($fieldsetMap['data']);
        switch ($organisation['type']['id']) {
            case self::ORG_TYPE_REGISTERED_COMPANY:
            case self::ORG_TYPE_LLP:
                // no-op; the full form is fine
                break;
            case self::ORG_TYPE_SOLE_TRADER:
                $fieldset->remove('name')->remove('companyNumber');
                $form->remove($fieldsetMap['table']);
                break;
            case self::ORG_TYPE_PARTNERSHIP:
                $fieldset->remove('companyNumber');
                $fieldset->get('name')->setLabel($fieldset->get('name')->getLabel() . '.partnership');
                $form->remove($fieldsetMap['table']);
                break;
            case self::ORG_TYPE_OTHER:
                $fieldset->remove('companyNumber')->remove('tradingNames');
                $fieldset->get('name')->setLabel($fieldset->get('name')->getLabel() . '.other');
                $form->remove($fieldsetMap['table']);
                break;
        }

        // If this is a review, remove the trading names section
        if ( $organisation['type']['id'] != self::ORG_TYPE_OTHER ) {
            if ( $options['isReview'] ) {
                $fieldset->remove('tradingNames')->remove('edit_business_type');
                $fieldset->get('companyNumber')->remove('submit_lookup_company');
            } else {
                $fieldset->remove('tradingNamesReview');
            }
        } else {
            $fieldset->remove('tradingNamesReview');
        }

        return $form;
    }

    /**
     * Conditionally alter the form
     *
     * @param Form $form
     * @return Form
     */
    protected function alterForm($form)
    {
        $options=array(
            'isReview' => false
        );
        $form=$this->makeFormAlterations($form, $this, $options);

        return $form;
    }

    /**
     * Post set form data method
     *
     * @param Form $form
     * @return Form
     */
    protected function postSetFormData($form)
    {
        return $this->processAddTradingName($form);
    }

    /**
     * Process load data for form
     *
     * @param array $data
     * @return array
     */
    protected function processLoad($data)
    {
        $licence = $data['licence'];
        $organisation = $licence['organisation'];

        $tradingNames = [];

        if ( isset($licence['organisation']['tradingNames']) ) {
            foreach ($licence['organisation']['tradingNames'] as $tradingName) {
                $tradingNames[] = ['text' => $tradingName['name']];
            }
        }

        $tradingNames[] = ['text' => ''];

        $map = [
            'tradingNames' => [
                'trading_name' => $tradingNames,
            ],
            'companyNumber' => [
                'company_number' => $organisation['companyOrLlpNo']
            ]
        ];

        $data = [
            'data' => array_merge($organisation, $map)
        ];

        if (isset($data['data']['type']['id'])) {
            $data['data']['type'] = $data['data']['type']['id'];
        }

        return $data;
    }

    /**
     * Override getForm
     *
     * @param string $type
     * @return Form
     */
    protected function getForm($type)
    {
        return $this->processLookupCompany(parent::getForm($type));
    }

    /**
     * Generate form with data
     *
     * @todo Should this really be public?
     *
     * @param string $name
     * @param callable $callback
     * @param array $data
     * @param array $tables
     * @return Form
     */
    public function generateFormWithData($name, $callback, $data = null, $tables = false)
    {
        $request = $this->getRequest();

        $post = (array)$request->getPost();

        if (isset($post['data']['tradingNames']['submit_add_trading_name'])) {

            $this->setPersist(false);
        }

        $form = parent::generateFormWithData($name, $callback, $data, $tables);

        return $form;
    }

    /**
     * Process add trading name
     *
     * @param Form $form
     * @return Form
     */
    protected function processAddTradingName($form)
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $form;
        }

        $post = (array)$request->getPost();

        if (isset($post['data']['tradingNames']['submit_add_trading_name'])) {

            $form->setValidationGroup(array('data' => ['tradingNames']));

            $form->setData($post);

            if ($form->isValid()) {

                $tradingNames = $form->getData()['data']['tradingNames']['trading_name'];

                //remove existing entries from collection and check for empty entries
                foreach ($tradingNames as $key => $val) {
                    if (strlen(trim($val['text'])) == 0) {
                        unset($tradingNames[$key]);
                    }
                }

                $tradingNames[] = ['text' => ''];

                $form->get('data')->get('tradingNames')->get('trading_name')->populateValues($tradingNames);
            }
        }

        return $form;
    }

    /**
     * Process lookup company
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function processLookupCompany(\Zend\Form\Form $form)
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $form;
        }

        $post = (array)$request->getPost()['data'];

        if (isset($post['companyNumber']['submit_lookup_company'])) {

            $this->setPersist(false);

            if (strlen(trim($post['companyNumber']['company_number'])) != 8) {

                $form->get('data')->get('companyNumber')->setMessages(
                    array(
                        'company_number' => array(
                            'The input must be 8 characters long'
                        )
                    )
                );

            } else {

                $result = $this->makeRestCall(
                    'CompaniesHouse',
                    'GET',
                    [
                        'type' => 'numberSearch',
                        'value' => $post['companyNumber']['company_number']
                    ]
                );

                if ($result['Count'] == 1) {

                    $companyName = $result['Results'][0]['CompanyName'];
                    $post['name'] = $companyName;
                    $this->setFieldValue('data', $post);

                } else {

                    $form->get('data')->get('companyNumber')->setMessages(
                        array(
                            'company_number' => array(
                                'Sorry, we couldn\'t find any matching companies, please try again or enter your '
                                . 'details manually below'
                            )
                        )
                    );
                }
            }
        }

        return $form;
    }

    /**
     * Add subsidiary company
     */
    public function addAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit subsidiary company
     */
    public function editAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete subsidiary company
     *
     * @return Response
     */
    public function deleteAction()
    {
        return $this->delete();
    }

    /**
     * Action save
     *
     * @param array $data
     * @param string $service
     */
    protected function actionSave($data, $service = null)
    {
        $extraBundle = array(
            'children' => array(
                'type' => array(
                    'properties' => array(
                        'id'
                    )
                )
            )
        );

        $organisation = $this->getOrganisationData($extraBundle);
        $data['organisation'] = $organisation['id'];
        parent::actionSave($data, 'CompanySubsidiary');
    }

    /**
     * Format the data for the form
     *
     * @param array $data
     * @return array
     */
    protected function processActionLoad($data)
    {
        return array('data' => $data);
    }

    /**
     * Get the form table data
     *
     * @return array
     */
    protected function getFormTableData($id, $table)
    {
        $data=$this->getSummaryTableData($id, $this, "");

        return $data;
    }

    /**
     * Get the form table data for the review stage
     *
     * @param int $id
     * @param string $table
     */
    public static function getSummaryTableData($applicationId, $context, $tableName)
    {
        $applicationData = $context->makeRestCall(
            'Application',
            'GET',
            array('id' => $applicationId),
            self::$applicationBundle
        );

        $loadData = $context->makeRestCall(
            'CompanySubsidiary',
            'GET',
            array('organisation' => $applicationData['licence']['organisation']['id']),
            self::$subCompanyBundle
        );

        return $loadData;
    }
}
