<?php

/**
 * People Controller
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\YourBusiness;

/**
 * People Controller
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PeopleController extends YourBusinessController
{
    /**
     *   Application ID bundle
     */
    public static $applicationBundle = array(
        'properties' => array(
            'id',
            'version',
        ),
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
                        )
                    )
                )
            )
        )
    );

    /**
     * Form tables name
     *
     * @var string
     */
    protected $formTables = array(
        'table' => 'application_your-business_people_in_form'
    );

    /**
     * Action data map
     *
     * @var array
     */
    protected $actionDataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
            )
        )
    );

    /**
     * Holds the action service
     *
     * @var string
     */
    protected $actionService = 'Person';

    /**
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->populatePeople();

        return $this->renderSection();
    }

    /**
     * Get the form table data
     *
     * @return array
     */
    protected function getFormTableData($id, $table)
    {
        $tableData=$this->getSummaryTableData($id, $this, '');
        return $tableData;
    }

    /**
     * Get the form table data
     */
    public static function getSummaryTableData($applicationId, $context, $tableName)
    {
        $org = $context->makeRestCall(
            'Application',
            'GET',
            array('id' => $applicationId),
            self::$applicationBundle
        );

        $bundle = array(
            'properties' => array('position'),
            'children' => array(
                'person' => array(
                    'properties' => array(
                        'id',
                        'title',
                        'forename',
                        'familyName',
                        'birthDate',
                        'otherName'
                    )
                )
            )
        );

        $data = $context->makeRestCall(
            'OrganisationPerson',
            'GET',
            array('organisation' => $org['id']),
            $bundle
        );

        $tableData = array();

        foreach ($data['Results'] as $result) {
            if (is_array($result['person']) && isset($result['position'])) {
                $tableData[] = array_merge($result['person'], array('position' => $result['position']));
            } else {
                $tableData[] = $result['person'];
            }
        }

        return $tableData;

    }

    /**
     * Add customisation to the table
     *
     * @param Form $form
     * @return Form
     */
    protected function alterForm($form)
    {
        $table = $form->get('table')->get('table')->getTable();

        $bundle = array(
            'children' => array(
                'type' => array(
                    'properties' => array('id')
                )
            )
        );

        $org = $this->getOrganisationData($bundle);

        $translator = $this->getServiceLocator()->get('translator');
        $guidance = $form->get('guidance')->get('guidance');

        switch ($org['type']) {
            case self::ORG_TYPE_REGISTERED_COMPANY:
                $table->setVariable(
                    'title',
                    $translator->translate('selfserve-app-subSection-your-business-people-tableHeaderDirectors')
                );
                $guidance->setValue($translator->translate('selfserve-app-subSection-your-business-people-guidanceLC'));
                break;
            case self::ORG_TYPE_LLP:
                $table->setVariable(
                    'title',
                    $translator->translate('selfserve-app-subSection-your-business-people-tableHeaderPartners')
                );
                $guidance->setValue(
                    $translator->translate('selfserve-app-subSection-your-business-people-guidanceLLP')
                );
                break;
            case self::ORG_TYPE_PARTNERSHIP:
                $table->setVariable(
                    'title',
                    $translator->translate('selfserve-app-subSection-your-business-people-tableHeaderPartners')
                );
                $guidance->setValue($translator->translate('selfserve-app-subSection-your-business-people-guidanceP'));
                break;
            case self::ORG_TYPE_OTHER:
                $table->setVariable(
                    'title',
                    $translator->translate('selfserve-app-subSection-your-business-people-tableHeaderPeople')
                );
                $guidance->setValue($translator->translate('selfserve-app-subSection-your-business-people-guidanceO'));
                break;
            default:
                break;
        }

        if ($org['type'] != self::ORG_TYPE_OTHER) {
            $table->removeColumn('position');
        }

        return $form;
    }

    /**
     * Customize form
     *
     * @param Form $form
     */
    protected function alterActionForm($form)
    {
        $bundle = array(
            'children' => array(
                'type' => array(
                    'properties' => array(
                        'id'
                    )
                )
            )
        );

        $orgType = $this->getOrganisationData($bundle);

        if ($orgType['type']['id'] != self::ORG_TYPE_OTHER) {
            $form->get('data')->remove('position');
        }
        return $form;
    }

    /**
     * Add person
     */
    public function addAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit person
     */
    public function editAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete person
     *
     * @return Response
     */
    public function deleteAction()
    {
        $id = $this->getActionId();

        $org = $this->getOrganisationData();
        $results = $this->makeRestCall(
            'OrganisationPerson',
            'GET',
            array('person' => $id, 'organisation' => $org['id']),
            array('properties' => array('id'))
        );
        if (isset($results['Count']) && $results['Count']) {
            $this->makeRestCall('OrganisationPerson', 'DELETE', array('id' => $results['Results'][0]['id']));
        }

        // we should delete the person only if it is not connected with any other organisation
        $results = $this->makeRestCall(
            'OrganisationPerson',
            'GET',
            array('person' => $id),
            array('properties' => array('id'))
        );
        if (isset($results['Count']) && !$results['Count']) {
            return $this->delete();
        }

        return $this->redirectToIndex();
    }

    /**
     * Process action load data
     *
     * @param array $data
     * @return array
     */
    protected function processActionLoad($data)
    {
        $org = $this->getOrganisationData();
        $position = $this->getPosition($org['id'], $this->params()->fromRoute('id'));
        $data['position'] = $position;
        return array('data' => parent::processActionLoad($data));
    }

    /**
     * Save method
     *
     * @param array $data
     * @parem string $service
     */
    protected function save($validData, $service = null)
    {
    }

    /**
     * Action save
     *
     * @param array $data
     * @param string $service
     */
    protected function actionSave($data, $service = null)
    {
        $person = parent::actionSave($data, 'Person');

        $org = $this->getOrganisationData();

        $needToSaveOrganisationPerson = false;
        // If we are creating a person, we need to link them to the organisation,
        // oterhwise we might need to update person's position
        if ($this->getActionName() == 'add') {
            $orgPersonData = array(
                'organisation' => $org['id'],
                'person' => $person['id'],
                'position' => isset($data['position']) ? $data['position'] : ''
            );
            $needToSaveOrganisationPerson = true;
        } elseif ($this->getActionName() == 'edit' && self::ORG_TYPE_OTHER) {
            $orgPerson = $this->makeRestCall(
                'OrganisationPerson',
                'GET', array('organisation' => $org['id'], 'person' => $data['id'])
            );
            $orgPersonData = array(
                'position' => isset($data['position']) ? $data['position'] : '',
                'id' => $orgPerson['Results'][0]['id'],
                'version' => $orgPerson['Results'][0]['version'],
            );
            $needToSaveOrganisationPerson = true;
        }
        if ($needToSaveOrganisationPerson) {
            parent::actionSave($orgPersonData, 'OrganisationPerson');
        }
    }

    /**
     * Get person's position from OrganisatonType
     *
     * @param int $orgId
     * @param int $personId
     * @return int
     */
    protected function getPosition($orgId = null, $personId = null)
    {
        $position = '';
        if ($orgId && $personId) {
            $orgPerson = $this->makeRestCall(
                'OrganisationPerson',
                'GET', array('organisation' => $orgId, 'person' => $personId)
            );
            if (is_array($orgPerson) && $orgPerson['Count'] > 0) {
                $position = $orgPerson['Results'][0]['position'];
            }
        }
        return $position;
    }

    /**
     * We should have this method to display empty form
     *
     * @param int $id
     * @param array
     */
    protected function load($id)
    {
        return array();
    }

    /**
     * Pre-populate people for company
     *
     */
    protected function populatePeople()
    {
        $bundle = array(
            'properties' => array('companyOrLlpNo'),
            'children' => array(
                'type' => array(
                    'properties' => array('id')
                )
            )
        );

        $org = $this->getOrganisationData($bundle);

        $orgTypesOnCompaniesHouse = array(
            self::ORG_TYPE_LLP,
            self::ORG_TYPE_REGISTERED_COMPANY
        );

        // If we are not a limited company or LLP just bail
        // OR if we have already added people
        // OR if we don't have a company number
        if (!in_array($org['type']['id'], $orgTypesOnCompaniesHouse)
            || $this->peopleAdded()
            || !preg_match('/^[A-Z0-9]{8}$/', $org['companyOrLlpNo'])) {
            return;
        }

        $searchData = array(
            'type' => 'currentCompanyOfficers',
            'value' => $org['companyOrLlpNo']
        );

        $result = $this->makeRestCall('CompaniesHouse', 'GET', $searchData);

        if (is_array($result) && array_key_exists('Results', $result) && count($result['Results'])) {

            // @todo We need a better way to handle this, far too many rest calls could happen
            foreach ($result['Results'] as $person) {

                // Create a person
                $person = $this->makeRestCall('Person', 'POST', $person);

                // If we have a person id
                if (isset($person['id'])) {

                    $organisationPersonData = array(
                        'organisation' => $org['id'],
                        'person' => $person['id']
                    );

                    $this->makeRestCall('OrganisationPerson', 'POST', $organisationPersonData);
                }
            }
        }
    }

    /**
     * Determine if people already added for current application
     *
     * @return bool
     */
    protected function peopleAdded()
    {
        $org = $this->getOrganisationData();

        $bundle = array('properties' => array('id'));

        $data = $this->makeRestCall('OrganisationPerson', 'GET', array('organisation' => $org['id']), $bundle);

        return (array_key_exists('Count', $data) && $data['Count'] > 0);
    }
}
