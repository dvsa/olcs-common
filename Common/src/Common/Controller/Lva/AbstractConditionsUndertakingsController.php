<?php

/**
 * Abstract Conditions Undertakings Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Interfaces\AdapterAwareInterface;

/**
 * Abstract Conditions Undertakings Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractConditionsUndertakingsController extends AbstractController implements AdapterAwareInterface
{
    use Traits\AdapterAwareTrait,
        Traits\CrudTableTrait;

    protected $section = 'conditions_undertakings';
    protected $baseRoute = 'lva-%s/conditions_undertakings';

    /**
     * Conditions Undertakings section
     *
     * @return mixed
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();

            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction);
            }

            $this->updateCompletion();

            return $this->completeSection($this->section);
        }

        $form = $this->getForm();

        $this->getAdapter()->attachMainScripts();

        return $this->render($this->section, $form, $this->getRenderVariables());
    }

    /**
     * Add action, just wraps addOrEdit
     *
     * @return mixed
     */
    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    /**
     * Add action, just wraps addOrEdit
     *
     * @return mixed
     */
    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    /**
     * Common logic between add/edit
     *
     * @param string $mode
     * @return mi
     */
    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();
        $formData = [];
        $id = $this->params('child_id');
        if ($request->isPost()) {
            $formData = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $response = $this->handleQuery(
                \Dvsa\Olcs\Transfer\Query\ConditionUndertaking\Get::create(['id' => $id])
            );
            if (!$response->isOk()) {
                throw new \RuntimeException('Failed to get ConditionUndertaking');
            }
            $conditionUndertakingData = $response->getResult();
            if (!$this->getAdapter()->canEditRecord($conditionUndertakingData)) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('generic-cant-edit-message');

                return $this->redirect()->toRouteAjax(null, ['action' => null], [], true);
            }
            $formData = [
                'fields' => [
                    'id' => $conditionUndertakingData['id'],
                    'version' => $conditionUndertakingData['version'],
                    'type' => $conditionUndertakingData['conditionType']['id'],
                    'notes' => $conditionUndertakingData['notes'],
                    'fulfilled' => $conditionUndertakingData['isFulfilled'],
                    'attachedTo' => 'cat_lic',
                    'conditionCategory' => $conditionUndertakingData['conditionCategory'],
                ]
            ];

            if (isset($conditionUndertakingData['operatingCentre']['id'])) {
                $formData['fields']['attachedTo'] = $conditionUndertakingData['operatingCentre']['id'];
            }
        }

        $form = $this->getConditionUndertakingForm();

        $this->getAdapter()->alterForm($form, $this->getData());

        $form->setData($formData);

        if ($request->isPost() && $form->isValid()) {
            if (empty($formData['fields']['id'])) {
                $this->create($formData);
            } else {
                $this->update($formData);
            }

            return $this->handlePostSave(null, false);
        }

        return $this->render($mode . '_condition_undertaking', $form);
    }

    protected function updateCompletion()
    {
        if ($this->lva != 'licence') {
            $this->handleCommand(
                \Dvsa\Olcs\Transfer\Command\Application\UpdateCompletion::create(
                    ['id' => $this->getIdentifier(), 'section' => 'conditionsUndertakings']
                )
            );
        }
    }

    /**
     * Get Data for the licence/application and operating centres
     *
     * @return array
     * @throws \RuntimeException
     */
    protected function getData()
    {
        if ($this->lva === 'licence') {
            $command = \Dvsa\Olcs\Transfer\Query\Licence\OperatingCentre::create(['id' => $this->getIdentifier()]);
        } else {
            $command = \Dvsa\Olcs\Transfer\Query\Application\OperatingCentre::create(['id' => $this->getIdentifier()]);
        }
        $response = $this->handleQuery($command);
        if (!$response->isOk()) {
            throw new \RuntimeException('Failed getting operating centre data');
        }

        return $response->getResult();
    }

    /**
     * Create a new ConditionUndertaking
     *
     * @param array $formData
     * @throws \RuntimeException
     */
    protected function create($formData)
    {
        $command = $this->getAdapter()->getCreateCommand($formData, $this->lva, $this->getIdentifier());

        $response = $this->handleCommand($command);
        if (!$response->isOk()) {
            throw new \RuntimeException(
                'Failed creating a ConditionUndertaking - '. print_r($response->getResult(), true)
            );
        }
    }

    /**
     * Update a ConditionUndertaking
     *
     * @param array $formData
     * @throws \RuntimeException
     */
    protected function update($formData)
    {
        $command = $this->getAdapter()->getUpdateCommand($formData, $this->getIdentifier());

        $response = $this->handleCommand($command);
        if (!$response->isOk()) {
            throw new \RuntimeException(
                'Failed updating a ConditionUndertaking - '. print_r($response->getResult(), true)
            );
        }
    }

    /**
     * Delete one or more ConditionUndertaking
     * @throws \RuntimeException
     */
    protected function delete()
    {
        $id = $this->params('child_id');
        $ids = explode(',', $id);

        $command = $this->getAdapter()->getDeleteCommand($this->getIdentifier(), $ids);

        $response = $this->handleCommand($command);
        if (!$response->isOk()) {
            throw new \RuntimeException(
                'Failed deleting a ConditionUndertaking - '. print_r($response->getResult(), true)
            );
        }
    }

    /**
     * Get the add/edit form
     *
     * @return \Laminas\Form\Form
     */
    protected function getConditionUndertakingForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('ConditionUndertaking');
    }

    /**
     * Get conditions undertakings form
     *
     * @return \Laminas\Form\Form
     */
    protected function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm();

        $formHelper->populateFormTable($form->get('table'), $this->getTable());

        return $form;
    }

    /**
     * Grab the table object
     *
     * @return \Common\Service\Table\TableBuilder
     */
    protected function getTable()
    {
        $tableBuilder = $this->getServiceLocator()->get('Table');

        $table = $tableBuilder->prepareTable(
            $this->getAdapter()->getTableName(),
            $this->getTableData()
        );

        $this->getAdapter()->alterTable($table);

        return $table;
    }

    /**
     * Grab the table data, list of ConditionUndertaking
     *
     * @return array
     */
    protected function getTableData()
    {
        $la = ($this->lva === 'licence') ? 'licence' : 'application';
        $response = $this->handleQuery(
            \Dvsa\Olcs\Transfer\Query\ConditionUndertaking\GetList::create([$la => $this->getIdentifier()])
        );
        if (!$response->isOk()) {
            throw new \RuntimeException('Failed to get ConditionUndertaking data.');
        }
        $results = $response->getResult()['results'];

        $data = [];
        foreach ($results as $row) {
            if ($row['action'] == '') {
                $row['action'] = 'E';
            }
            switch ($row['action']) {
                case 'U':
                    $data[$row['licConditionVariation']['id']]['action'] = 'C';
                    break;
                case 'D':
                    unset($data[$row['licConditionVariation']['id']]);
                    break;
            }
            $data[$row['id']] = $row;
        }
        return $data;
    }

    /**
     * Get any override variables to use for rendering.
     *
     * @return array
     */
    protected function getRenderVariables()
    {
        return array();
    }
}
