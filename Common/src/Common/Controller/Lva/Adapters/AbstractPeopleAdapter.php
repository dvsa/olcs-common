<?php

namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\AbstractController;
use Common\Controller\Lva\Interfaces\PeopleAdapterInterface;
use Common\RefData;
use Common\Service\Table\TableBuilder;
use Laminas\Form\Form;
use Laminas\Mvc\Controller\Plugin\FlashMessenger;

/**
 * Abstract people adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractPeopleAdapter extends AbstractControllerAwareAdapter implements PeopleAdapterInterface
{
    const ACTION_ADDED = 'A';
    const ACTION_EXISTING = 'E';
    const ACTION_CURRENT = 'C';
    const ACTION_UPDATED = 'U';
    const ACTION_DELETED = 'D';

    const SOURCE_APPLICATION = 'A';
    const SOURCE_ORGANISATION = 'O';

    protected $tableData = [];

    private $licence;
    private $data;
    private $application;

    /**
     * Load the people dataa
     *
     * @param string $lva Lic|App|Var
     * @param int    $id  Either an Application or Licence ID
     *
     * @return bool If successful
     */
    public function loadPeopleData($lva, $id)
    {
        if ($lva === AbstractController::LVA_LIC) {
            $this->loadPeopleDataForLicence($id);
        } else {
            $this->loadPeopleDataForApplication($id);
        }
        return true;
    }

    /**
     * Load People data for a Licence
     *
     * @param int $licenceId Licence Id
     *
     * @return void
     */
    protected function loadPeopleDataForLicence($licenceId)
    {
        $command = \Dvsa\Olcs\Transfer\Query\Licence\People::create(['id' => $licenceId]);

        $response = $this->handleQuery($command);
        if (!$response->isOk()) {
            throw new \RuntimeException('Failed to load people data');
        }
        $data = $response->getResult();

        $this->data = $data;
        $this->licence = $data;
    }

    /**
     * Load People data for an Application/Variation
     *
     * @param int $applicationId Application Id
     *
     * @return void
     */
    protected function loadPeopleDataForApplication($applicationId)
    {
        $command = \Dvsa\Olcs\Transfer\Query\Application\People::create(['id' => $applicationId]);

        $response = $this->handleQuery($command);
        if (!$response->isOk()) {
            throw new \RuntimeException('Failed to load people data');
        }
        $data = $response->getResult();

        $this->data = $data;
        $this->application = $data;
        $this->licence = $data['licence'];
    }

    /**
     * Handle query
     *
     * @param \Dvsa\Olcs\Transfer\Query\QueryInterface $command Query
     *
     * @return \Common\Service\Cqrs\Response
     */
    protected function handleQuery(\Dvsa\Olcs\Transfer\Query\QueryInterface $command)
    {
        $serviceLocator = $this->getServiceLocator();

        $query = $serviceLocator->get('TransferAnnotationBuilder')->createQuery($command);
        return $serviceLocator->get('QueryService')->send($query);
    }

    /**
     * Handle Command
     *
     * @param \Dvsa\Olcs\Transfer\Command\CommandInterface $command Commnand
     *
     * @return \Common\Service\Cqrs\Response
     */
    protected function handleCommand(\Dvsa\Olcs\Transfer\Command\CommandInterface $command)
    {
        $serviceLocator = $this->getServiceLocator();

        $annotationBuilder = $serviceLocator->get('TransferAnnotationBuilder');
        $commandService = $serviceLocator->get('CommandService');

        return $commandService->send($annotationBuilder->createCommand($command));
    }

    /**
     * Has Inforce Licences
     *
     * @return bool
     */
    public function hasInforceLicences()
    {
        return $this->data['hasInforceLicences'];
    }

    /**
     * Is Exceptional Organisation
     *
     * @return bool
     */
    public function isExceptionalOrganisation()
    {
        return $this->data['isExceptionalType'];
    }

    /**
     * Get Organisation data
     *
     * @return array
     */
    public function getOrganisation()
    {
        return $this->licence['organisation'];
    }

    /**
     * Get organisation Id
     *
     * @return int
     */
    public function getOrganisationId()
    {
        return $this->licence['organisation']['id'];
    }

    /**
     * Get the licence
     *
     * @return mixed
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Get the Application
     *
     * @return mixed
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Organisation is Sole Traider
     *
     * @return bool
     */
    public function isSoleTrader()
    {
        return $this->data['isSoleTrader'];
    }

    /**
     * Organisation is Partner Ship
     *
     * @return bool
     */
    public function isPartnership()
    {
        return $this->getOrganisationType() === \Common\RefData::ORG_TYPE_PARTNERSHIP;
    }

    /**
     * Check if has more than one suspended Curtailed licences
     *
     * @return mixed
     */
    public function hasMoreThanOneValidCurtailedOrSuspendedLicences()
    {
        return $this->data['hasMoreThanOneValidCurtailedOrSuspendedLicences'];
    }

    /**
     * Is the Organisation an LLP or LTD company
     *
     * @return bool
     */
    public function isOrganisationLimited()
    {
        $limitedTypes = [
            \Common\RefData::ORG_TYPE_LLP,
            \Common\RefData::ORG_TYPE_RC,
        ];
        return in_array($this->getOrganisationType(), $limitedTypes, false);
    }

    /**
     * Is the Organisation type other
     *
     * @return bool
     */
    public function isOrganisationOther()
    {
        $types = [
            \Common\RefData::ORG_TYPE_OTHER,
        ];
        return in_array($this->getOrganisationType(), $types, false);
    }

    /**
     * use Deltas
     *
     * @return bool
     */
    public function useDeltas()
    {
        return (isset($this->data['useDeltas']) && $this->data['useDeltas']);
    }

    /**
     * Get and array of all people
     *
     * @return array
     */
    public function getPeople()
    {
        if ($this->getApplication()) {
            // need to merge the orgPeople with the appOrgPeople
            return $this->updateAndFilterTableData(
                $this->indexRows(self::SOURCE_ORGANISATION, $this->data['people']),
                $this->indexRows(self::SOURCE_APPLICATION, $this->data['application-people'])
            );
        }

        return $this->data['people'];
    }

    /**
     * Get person data
     *
     * @param int $personId Person Id
     *
     * @return array|false person data or false if not found
     */
    public function getPersonData($personId)
    {
        foreach ($this->getPeople() as $organisationPerson) {
            if ($organisationPerson['person']['id'] == $personId) {
                return $organisationPerson;
            }
        }

        return false;
    }

    /**
     * Get first person data
     *
     * @return array|false person data or false if not found
     */
    public function getFirstPersonData()
    {
        return (isset($this->getPeople()[0])) ? $this->getPeople()[0] : false;
    }

    /**
     * Abstract Method implementation
     *
     * @return void
     */
    public function addMessages()
    {
    }

    /**
     * Alter form for organisation
     *
     * @param Form         $form  form
     * @param TableBuilder $table table
     *
     * @return void
     */
    public function alterFormForOrganisation(Form $form, $table)
    {
        $labelTextForOrganisation = $this->getAddLabelTextForOrganisation();

        $action = $table->getAction('add');
        $table->removeAction('add');
        $action['label'] = $labelTextForOrganisation;
        $table->addAction('add', $action);
    }

    /**
     * Abstract method implementation
     *
     * @param Form $form form
     *
     * @return void
     */
    public function alterAddOrEditFormForOrganisation(Form $form)
    {
    }

    /**
     * Can the form be modified
     *
     * @return bool
     */
    public function canModify()
    {
        return true;
    }

    /**
     * Create table
     *
     * @return TableBuilder
     */
    public function createTable()
    {
        /** @var TableBuilder $table */
        $table = $this->getServiceLocator()
            ->get('Table')
            ->prepareTable($this->getTableConfig(), $this->getTableData());

        //  set empty message in depend of Organisation type
        if ($this->getOrganisationType() === RefData::ORG_TYPE_REGISTERED_COMPANY) {
            $table->setEmptyMessage('selfserve-app-subSection-your-business-people-ltd.table.empty-message');
        }
        return $table;
    }

    /**
     * Get the table data for the main form
     *
     * @return array
     */
    protected function getTableData()
    {
        if (empty($this->tableData)) {
            $this->tableData = $this->addNewStatuses(
                $this->formatTableData($this->getPeople())
            );
        }

        return $this->tableData;
    }

    /**
     * addNewStatuses function
     *
     * @param array $tableData Table Data
     *
     * @return array Table Data
     */
    private function addNewStatuses(array $tableData)
    {
        /** @var FlashMessenger $flashMessenger */
        $flashMessenger = $this->getController()->plugin('FlashMessenger');
        $newPersonIDs = $flashMessenger->getMessages(AbstractController::FLASH_MESSENGER_CREATED_PERSON_NAMESPACE);

        $newTableData = [];

        foreach ($tableData as $key => $person) {
            if (in_array($person['id'], $newPersonIDs)) {
                $person['status'] = 'new';
            } else {
                $person['status'] = null;
            }

            $newTableData[$key] = $person;
        }

        return $newTableData;
    }

    /**
     * Prepare data to display in table
     *
     * @param array $results Results
     *
     * @return array
     */
    protected function formatTableData($results)
    {
        $final = array();
        foreach ($results as $row) {
            // flatten the person's position if it's non null
            if (isset($row['position'])) {
                $row['person']['position'] = $row['position'];
            }
            // ... and action too
            if (isset($row['action'])) {
                $row['person']['action'] = $row['action'];
            }
            $final[] = $row['person'];
        }
        return $final;
    }

    /**
     * Get Licence Id
     *
     * @return int
     */
    public function getLicenceId()
    {
        return $this->getLicence()['id'];
    }

    /**
     * Get application Id
     *
     * @return int
     */
    public function getApplicationId()
    {
        return $this->getApplication()['id'];
    }

    /**
     * Get the Organisation Type ID eg "org_t_p"
     *
     * @return string
     */
    public function getOrganisationType()
    {
        $orgData = $this->getOrganisation();
        return $orgData['type']['id'];
    }

    /**
     * Get Licence Type
     *
     * @return mixed
     */
    public function getLicenceType()
    {
        if ($this->application !== null) {
            return $this->application['licenceType']['id'];
        }

        return $this->licence['licenceType']['id'];
    }

    /**
     * Delete a person from the organisation, and then delete the person if they are now an orphan
     *
     * @param array $ids list of identifiers of Deleted Persons
     *
     * @return bool
     */
    public function delete($ids)
    {
        $response = $this->handleCommand($this->getDeleteCommand(['personIds' => $ids]));
        /* @var $response \Common\Service\Cqrs\Response */
        if (!$response->isOk()) {
            throw new \RuntimeException('Error deleteing Org Person : ' . print_r($response->getResult(), true));
        }

        return true;
    }

    /**
     * Respore Persons
     *
     * @param array $ids list of identifiers of Restored Persons
     *
     * @return bool
     */
    public function restore($ids)
    {
        // Can only restore in an application\variation
        $response = $this->handleCommand(
            \Dvsa\Olcs\Transfer\Command\Application\RestorePeople::create(
                ['id' => $this->getApplicationId(), 'personIds' => $ids]
            )
        );
        if (!$response->isOk()) {
            throw new \RuntimeException('Error restoring Person : ' . print_r($response->getResult(), true));
        }

        return true;
    }

    /**
     * Create
     *
     * @param array $data Command Data
     *
     * @return bool
     */
    public function create($data)
    {
        $response = $this->handleCommand($this->getCreateCommand($data));
        if (!$response->isOk()) {
            throw new \RuntimeException('Error creating Person : ' . print_r($response->getResult(), true));
        }

        return true;
    }

    /**
     * Update
     *
     * @param array $data Update data
     *
     * @return bool
     */
    public function update($data)
    {
        $response = $this->handleCommand($this->getUpdateCommand($data));
        if (!$response->isOk()) {
            throw new \RuntimeException('Error updating Person : ' . print_r($response->getResult(), true));
        }

        return true;
    }

    /**
     * Get the backend command to create a Person
     *
     * @param array $params Params
     *
     * @return \Dvsa\Olcs\Transfer\Command\AbstractCommand
     */
    protected function getCreateCommand($params)
    {
        $params['id'] = $this->getLicenceId();
        return \Dvsa\Olcs\Transfer\Command\Licence\CreatePeople::create($params);
    }

    /**
     * Get the backend command to update a Person
     *
     * @param array $params Params
     *
     * @return \Dvsa\Olcs\Transfer\Command\AbstractCommand
     */
    protected function getUpdateCommand($params)
    {
        $params['person'] = $params['id'];
        $params['id'] = $this->getLicenceId();
        return \Dvsa\Olcs\Transfer\Command\Licence\UpdatePeople::create($params);
    }


    /**
     * Get the backend command to delete a Person
     *
     * @param array $params Params
     *
     * @return \Dvsa\Olcs\Transfer\Command\AbstractCommand
     */
    protected function getDeleteCommand($params)
    {
        $params['id'] = $this->getLicenceId();
        return \Dvsa\Olcs\Transfer\Command\Licence\DeletePeople::create($params);
    }

    /**
     * Get the name of the table config
     *
     * @return string
     */
    protected function getTableConfig()
    {
        return 'lva-people';
    }

    /**
     * Update and filter the table data for variations
     *
     * @param array $orgData         Org Data
     * @param array $applicationData Appl Data
     *
     * @return array
     */
    private function updateAndFilterTableData($orgData, $applicationData)
    {
        $data = array();

        foreach ($orgData as $id => $row) {
            if (!isset($applicationData[$id])) {
                // E for existing (No updates)
                $row['action'] = self::ACTION_EXISTING;
                $data[] = $row;
            } elseif ($applicationData[$id]['action'] === self::ACTION_UPDATED) {
                $row['action'] = self::ACTION_CURRENT;
                $data[] = $row;
            }
        }

        $data = array_merge($data, $applicationData);

        return $data;
    }

    /**
     * Attach id value as key to persons array (data)
     *
     * @param string $key  Key ???
     * @param array  $data Array of persons
     *
     * @return array
     */
    private function indexRows($key, $data)
    {
        $indexed = [];

        foreach ($data as $value) {
            // if we've got a link to an original person then that
            // trumps any other relation
            if (isset($value['originalPerson']['id'])) {
                $id = $value['originalPerson']['id'];
            } else {
                $id = $value['person']['id'];
            }
            $value['person']['source'] = $key;
            $indexed[$id] = $value;
        }

        return $indexed;
    }

    /**
     * Get the add label text
     * Defaults to null if organisation type not set
     *
     * @return mixed | null or string
     */
    public function getAddLabelTextForOrganisation()
    {
        $type = [
            RefData::ORG_TYPE_RC => 'lva.section.title.add_director',
            RefData::ORG_TYPE_LLP => 'lva.section.title.add_partner',
            RefData::ORG_TYPE_PARTNERSHIP => 'lva.section.title.add_partner',
            RefData::ORG_TYPE_OTHER => 'lva.section.title.add_person',
            RefData::ORG_TYPE_IRFO => 'lva.section.title.add_person'
        ];
        if (isset($type[$this->getOrganisationType()])) {
            return $type[$this->getOrganisationType()];
        }
        return null;
    }

    /**
     * amend licence people list
     *
     * @param TableBuilder $table table
     *
     * @return TableBuilder
     */
    public function amendLicencePeopleListTable(TableBuilder $table)
    {
        $table->setSetting(
            'crud',
            [
                'actions' => [
                    'add' => [
                        'label' => $this->getAddLabelTextForOrganisation()
                    ]
                ]
            ]
        );
        return $table;
    }
}
