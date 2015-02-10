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

            $this->postSave($this->section);

            return $this->completeSection($this->section);
        }

        $form = $this->getForm();

        $this->alterFormForLva($form);

        $this->getAdapter()->attachMainScripts();

        return $this->render($this->section, $form);
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

        $data = [];

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {

            $id = $this->params('child_id');

            if (!$this->getAdapter()->canEditRecord($id, $this->getIdentifier())) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('generic-cant-edit-message');

                return $this->redirect()->toRouteAjax(null, ['action' => null], [], true);
            }

            $data = $this->getConditionPenaltyDetails($id);
        }

        $form = $this->getConditionUndertakingForm();

        $this->getAdapter()->alterForm($form, $this->getIdentifier());

        $form->setData($data);

        if ($request->isPost() && $form->isValid()) {

            $data = $this->getAdapter()->processDataForSave($data, $this->getIdentifier());

            $this->getAdapter()->save($data['fields']);

            return $this->handlePostSave();
        }

        return $this->render($mode . '_condition_undertaking', $form);
    }

    /**
     * Delete 1 or more conditions
     */
    protected function delete()
    {
        $id = $this->params('child_id');

        $ids = explode(',', $id);

        foreach ($ids as $id) {
            $this->getAdapter()->delete($id, $this->getIdentifier());
        }
    }

    /**
     * Get the form data for a given id
     *
     * @param int $id
     * @return array
     */
    protected function getConditionPenaltyDetails($id)
    {
        $entity = $this->getServiceLocator()->get('Entity\ConditionUndertaking')->getCondition($id);

        $data = ['fields' => $this->replaceIds($entity)];

        return $this->getAdapter()->processDataForForm($data);
    }

    /**
     * Replace the children's array, with their ids
     *
     * @param array $data
     * @return array
     */
    protected function replaceIds($data)
    {
        foreach ($data as $key => $var) {
            if (isset($var['id'])) {
                $data[$key] = $var['id'];
            }
        }

        return $data;
    }

    /**
     * Get the add/edit form
     *
     * @return \Zend\Form\Form
     */
    protected function getConditionUndertakingForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('ConditionUndertakingForm');
    }

    /**
     * Get conditions undertakings form
     *
     * @return \Zend\Form\Form
     */
    protected function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\ConditionsUndertakings');

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
     * Grab the table data
     *
     * @return array
     */
    protected function getTableData()
    {
        return $this->getAdapter()->getTableData($this->getIdentifier());
    }
}
