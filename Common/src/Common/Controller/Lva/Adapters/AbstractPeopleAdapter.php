<?php

namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\AbstractController;
use Common\Controller\Lva\Interfaces\PeopleAdapterInterface;
use Common\Controller\Plugin\HandleQuery;
use Common\RefData;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Table\TableBuilder;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Psr\Container\ContainerInterface;
use Laminas\Form\Form;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;

abstract class AbstractPeopleAdapter extends AbstractControllerAwareAdapter implements PeopleAdapterInterface
{
    public const ACTION_ADDED = 'A';

    public const ACTION_EXISTING = 'E';

    public const ACTION_CURRENT = 'C';

    public const ACTION_UPDATED = 'U';

    public const ACTION_DELETED = 'D';

    public const SOURCE_APPLICATION = 'A';

    public const SOURCE_ORGANISATION = 'O';

    protected array $tableData = [];

    private int $licence;

    private array $data;

    private int $application;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function loadPeopleData(string $lva, int $id): bool
    {
        if ($lva === AbstractController::LVA_LIC) {
            $this->loadPeopleDataForLicence($id);
        } else {
            $this->loadPeopleDataForApplication($id);
        }

        return true;
    }

    protected function loadPeopleDataForLicence(int $licenceId): void
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

    protected function loadPeopleDataForApplication(int $applicationId): void
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

    protected function handleQuery(\Dvsa\Olcs\Transfer\Query\QueryInterface $command): \Common\Service\Cqrs\Response
    {
        return $this->container->get('ControllerPluginManager')->get(HandleQuery::class)->__invoke($command);
    }

    protected function handleCommand(\Dvsa\Olcs\Transfer\Command\CommandInterface $command): \Common\Service\Cqrs\Response
    {
        $annotationBuilder = $this->container->get(AnnotationBuilder::class);
        $commandService = $this->container->get(CommandService::class);

        return $commandService->send($annotationBuilder->createCommand($command));
    }

    public function hasInforceLicences(): bool
    {
        return $this->data['hasInforceLicences'];
    }

    public function isExceptionalOrganisation(): bool
    {
        return $this->data['isExceptionalType'];
    }

    public function getOrganisation(): ?array
    {
        return $this->licence['organisation'] ?? null;
    }

    public function getOrganisationId(): int
    {
        return $this->licence['organisation']['id'];
    }

    public function getLicence(): mixed
    {
        return $this->licence;
    }

    public function getApplication(): mixed
    {
        return $this->application;
    }

    public function isSoleTrader(): bool
    {
        return $this->data['isSoleTrader'];
    }

    public function isPartnership(): bool
    {
        return $this->getOrganisationType() === \Common\RefData::ORG_TYPE_PARTNERSHIP;
    }

    public function hasMoreThanOneValidCurtailedOrSuspendedLicences(): mixed
    {
        return $this->data['hasMoreThanOneValidCurtailedOrSuspendedLicences'];
    }

    public function isOrganisationLimited(): bool
    {
        $limitedTypes = [
            \Common\RefData::ORG_TYPE_LLP,
            \Common\RefData::ORG_TYPE_RC,
        ];
        return in_array($this->getOrganisationType(), $limitedTypes, false);
    }

    public function isOrganisationOther(): bool
    {
        $types = [
            \Common\RefData::ORG_TYPE_OTHER,
        ];
        return in_array($this->getOrganisationType(), $types, false);
    }

    public function useDeltas(): bool
    {
        return (isset($this->data['useDeltas']) && $this->data['useDeltas']);
    }

    public function getPeople(): ?array
    {
        if ($this->getApplication()) {
            // need to merge the orgPeople with the appOrgPeople
            return $this->updateAndFilterTableData(
                $this->indexRows(self::SOURCE_ORGANISATION, $this->data['people']),
                $this->indexRows(self::SOURCE_APPLICATION, $this->data['application-people'])
            );
        }

        return $this->data['people'] ?? null;
    }

    /**
     * Get person data
     *
     * @param int $personId Person Id
     *
     * @return array|false person data or false if not found
     */
    public function getPersonData(int $personId): bool|array
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
        return $this->getPeople()[0] ?? false;
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
     */
    public function alterFormForOrganisation(Form $form, $table): void
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

    public function canModify(): bool
    {
        return true;
    }

    public function createTable(): TableBuilder
    {
        /** @var TableBuilder $table */
        $table = $this->container
            ->get('Table')
            ->prepareTable($this->getTableConfig(), $this->getTableData());

        //  set empty message in depend of Organisation type
        if ($this->getOrganisationType() === RefData::ORG_TYPE_REGISTERED_COMPANY) {
            $table->setEmptyMessage('selfserve-app-subSection-your-business-people-ltd.table.empty-message');
        }

        return $table;
    }

    protected function getTableData(): array
    {
        if (empty($this->tableData)) {
            $this->tableData = $this->addNewStatuses(
                $this->formatTableData($this->getPeople())
            );
        }

        return $this->tableData;
    }

    private function addNewStatuses(array $tableData): array
    {
        /** @var FlashMessenger $flashMessenger */
        $flashMessenger = $this->container->get('ControllerPluginManager')->get(FlashMessenger::class);
        $newPersonIDs = $flashMessenger->getMessages(AbstractController::FLASH_MESSENGER_CREATED_PERSON_NAMESPACE);

        $newTableData = [];

        foreach ($tableData as $key => $person) {
            $person['status'] = in_array($person['id'], $newPersonIDs) ? 'new' : null;

            $newTableData[$key] = $person;
        }

        return $newTableData;
    }

    protected function formatTableData(array $results): array
    {
        $final = [];
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

    public function getLicenceId(): int
    {
        return $this->getLicence()['id'];
    }

    public function getApplicationId(): int
    {
        return $this->getApplication()['id'];
    }

    public function getOrganisationType(): ?string
    {
        $orgData = $this->getOrganisation();
        return $orgData['type']['id'] ?? null;
    }

    public function getLicenceType(): mixed
    {
        if ($this->application !== null) {
            return $this->application['licenceType']['id'];
        }

        return $this->licence['licenceType']['id'];
    }

    public function delete($ids): bool
    {
        $response = $this->handleCommand($this->getDeleteCommand(['personIds' => $ids]));
        /* @var $response \Common\Service\Cqrs\Response */
        if (!$response->isOk()) {
            throw new \RuntimeException('Error deleteing Org Person : ' . print_r($response->getResult(), true));
        }

        return true;
    }

    public function restore($ids): bool
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

    public function create(array $data): bool
    {
        $response = $this->handleCommand($this->getCreateCommand($data));
        if (!$response->isOk()) {
            throw new \RuntimeException('Error creating Person : ' . print_r($response->getResult(), true));
        }

        return true;
    }

    public function update(array $data): bool
    {
        $response = $this->handleCommand($this->getUpdateCommand($data));
        if (!$response->isOk()) {
            throw new \RuntimeException('Error updating Person : ' . print_r($response->getResult(), true));
        }

        return true;
    }

    protected function getCreateCommand(array $params): \Dvsa\Olcs\Transfer\Command\Licence\CreatePeople
    {
        $params['id'] = $this->getLicenceId();
        return \Dvsa\Olcs\Transfer\Command\Licence\CreatePeople::create($params);
    }

    protected function getUpdateCommand(array $params): \Dvsa\Olcs\Transfer\Command\Licence\UpdatePeople
    {
        $params['person'] = $params['id'];
        $params['id'] = $this->getLicenceId();
        return \Dvsa\Olcs\Transfer\Command\Licence\UpdatePeople::create($params);
    }


    protected function getDeleteCommand(array $params): \Dvsa\Olcs\Transfer\Command\Licence\DeletePeople
    {
        $params['id'] = $this->getLicenceId();
        return \Dvsa\Olcs\Transfer\Command\Licence\DeletePeople::create($params);
    }

    protected function getTableConfig(): string
    {
        return 'lva-people';
    }

    private function updateAndFilterTableData(array $orgData, array $applicationData): array
    {
        $data = [];

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

        return array_merge($data, $applicationData);
    }

    private function indexRows(string $key, array $data): array
    {
        $indexed = [];

        foreach ($data as $value) {
            // if we've got a link to an original person then that
            // trumps any other relation
            $id = $value['originalPerson']['id'] ?? $value['person']['id'];

            $value['person']['source'] = $key;
            $indexed[$id] = $value;
        }

        return $indexed;
    }

    public function getAddLabelTextForOrganisation(): ?string
    {
        $type = [
            RefData::ORG_TYPE_RC => 'lva.section.title.add_director',
            RefData::ORG_TYPE_LLP => 'lva.section.title.add_partner',
            RefData::ORG_TYPE_PARTNERSHIP => 'lva.section.title.add_partner',
            RefData::ORG_TYPE_OTHER => 'lva.section.title.add_person',
            RefData::ORG_TYPE_IRFO => 'lva.section.title.add_person'
        ];
        return $type[$this->getOrganisationType()] ?? null;
    }

    public function amendLicencePeopleListTable(TableBuilder $table): TableBuilder
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
