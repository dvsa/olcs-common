<?php

/**
 * Abstract people adapter
 *
 * Contains common logic which used to just live in the abstract
 * people controller, i.e. the "plain" unmodified behaviour
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Common\Controller\Lva\Interfaces\PeopleAdapterInterface;
use Common\Service\Entity\OrganisationEntityService;

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
    private $application;
    private $data;

    /**
     * Load the people dataa
     *
     * @param int $id Either an Application or Licence ID
     *
     * @return bool If successful
     */
    public function loadPeopleData($lva, $id)
    {
        if ($lva === 'licence') {
            $this->loadPeopleDataForLicence($id);
        } else {
            $this->loadPeopleDataForApplication($id);
        }

        if (count($this->getPeople()) === 0 &&
            ($this->getOrganisationType() == OrganisationEntityService::ORG_TYPE_LLP ||
            $this->getOrganisationType() == OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY)
            ) {
            $response = $this->handleCommand(
                \Dvsa\Olcs\Transfer\Command\OrganisationPerson\PopulateFromCompaniesHouse::create(
                    ['id' => $this->getOrganisationId()]
                )
            );
            if (!$response->isOk()) {
                return false;
            }
            // reload data after populate from companies house
            if ($lva === 'licence') {
                $this->loadPeopleDataForLicence($id);
            } else {
                $this->loadPeopleDataForApplication($id);
            }
        }

        return true;
    }

    /**
     * Load People data for a Licence
     *
     * @param int $licenceId
     *
     * @throws \RuntimeException
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
     * @param int $applicationId
     *
     * @throws \RuntimeException
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
     *
     * @param \Dvsa\Olcs\Transfer\Query\QueryInterface $command
     * @return
     */
    protected function handleQuery(\Dvsa\Olcs\Transfer\Query\QueryInterface $command)
    {
        $serviceLocator = $this->getServiceLocator();

        $query = $serviceLocator->get('TransferAnnotationBuilder')->createQuery($command);
        return $serviceLocator->get('QueryService')->send($query);
    }

    /**
     *
     * @param \Dvsa\Olcs\Transfer\Command\CommandInterface $command
     * @return
     */
    protected function handleCommand(\Dvsa\Olcs\Transfer\Command\CommandInterface $command)
    {
        $serviceLocator = $this->getServiceLocator();

        $annotationBuilder = $serviceLocator->get('TransferAnnotationBuilder');
        $commandService = $serviceLocator->get('CommandService');

        return $commandService->send($annotationBuilder->createCommand($command));
    }

    public function hasInforceLicences()
    {
        return $this->data['hasInforceLicences'];
    }

    public function isExceptionalOrganisation()
    {
        return $this->data['isExceptionalType'];
    }

    public function getOrganisation()
    {
        return $this->licence['organisation'];
    }

    public function getOrganisationId()
    {
        return $this->licence['organisation']['id'];
    }

    public function getLicence()
    {
        return $this->licence;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function isSoleTrader()
    {
        return $this->data['isSoleTrader'];
    }

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
        } else {
            return $this->data['people'];
        }
    }

    /**
     * Get person data
     *
     * @param int $personId
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

    public function addMessages()
    {
    }

    public function alterFormForOrganisation(Form $form, $table)
    {
    }

    public function alterAddOrEditFormForOrganisation(Form $form)
    {
    }

    public function canModify()
    {
        return true;
    }

    public function createTable()
    {
        return $this->getServiceLocator()
            ->get('Table')
            ->prepareTable($this->getTableConfig(), $this->getTableData());
    }

    /**
     * Get the table data for the main form
     *
     * @param int $orgId
     * @return array
     */
    protected function getTableData()
    {
        if (empty($this->tableData)) {
            $this->tableData = $this->formatTableData($this->getPeople());
        }
        return $this->tableData;
    }

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
     * @return int
     */
    public function getLicenceId()
    {
        return $this->getLicence()['id'];
    }

    /**
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
     * Delete a person from the organisation, and then delete the person if they are now an orphan
     *
     * @param array $ids
     */
    public function delete($ids)
    {
        $response = $this->handleCommand($this->getDeleteCommand(['personIds' => $ids]));
        /* @var $response \Common\Service\Cqrs\Response */
        if (!$response->isOk()) {
            throw new \RuntimeException('Error deleteing Org Person : '. print_r($response->getResult(), true));
        }

        return true;
    }

    public function restore($ids)
    {
        // Can only restore in an application\variation
        $response = $this->handleCommand(
            \Dvsa\Olcs\Transfer\Command\Application\RestorePeople::create(
                ['id' => $this->getApplicationId(), 'personIds' => $ids]
            )
        );
        /* @var $response \Common\Service\Cqrs\Response */
        if (!$response->isOk()) {
            throw new \RuntimeException('Error restoring Person : '. print_r($response->getResult(), true));
        }

        return true;
    }

    public function create($data)
    {
        $response = $this->handleCommand($this->getCreateCommand($data));
        /* @var $response \Common\Service\Cqrs\Response */
        if (!$response->isOk()) {
            throw new \RuntimeException('Error creating Person : '. print_r($response->getResult(), true));
        }

        return true;
    }

    public function update($data)
    {
        $response = $this->handleCommand($this->getUpdateCommand($data));
        /* @var $response \Common\Service\Cqrs\Response */
        if (!$response->isOk()) {
            throw new \RuntimeException('Error updating Person : '. print_r($response->getResult(), true));
        }

        return true;
    }

    /**
     * Get the backend command to create a Person
     *
     * @param array $params
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
     * @param array $params
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
     * @param array $params
     *
     * @return \Dvsa\Olcs\Transfer\Command\AbstractCommand
     */
    protected function getDeleteCommand($params)
    {
        $params['id'] = $this->getLicenceId();
        return \Dvsa\Olcs\Transfer\Command\Licence\DeletePeople::create($params);
    }


    protected function getTableConfig()
    {
        return 'lva-people';
    }

    /**
     * Update and filter the table data for variations
     *
     * @param array $orgData
     * @param array $applicationData
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
}
