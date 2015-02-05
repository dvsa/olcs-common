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

    /**
     * Conditions Undertakings section
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

            $this->postSave('conditions_undertakings');

            return $this->completeSection('conditions_undertakings');
        }

        $form = $this->getForm();

        $this->alterFormForLva($form);

        return $this->render('conditions_undertakings', $form);
    }

    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();
        $id = $this->params('child_id');

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = $this->getConditionPenaltyDetails($id);
        }

        $form = $this->getConditionUndertakingForm();

        $this->getAdapter()->alterForm($form, $this->getIdentifier());

        $form->setData($data);

        if ($request->isPost() && $form->isValid()) {

            $data = $this->getAdapter()->processDataForSave($data, $this->getIdentifier());

            $this->getAdapter()->save($data);

            return $this->handlePostSave();
        }

        return $this->render($mode . '_condition_undertaking', $form);
    }

    protected function delete()
    {
        $id = $this->params('child_id');

        $ids = explode(',', $id);

        $entityService = $this->getServiceLocator()->get('Entity\ConditionUndertaking');

        foreach ($ids as $id) {
            $entityService->delete($id);
        }
    }

    protected function getConditionPenaltyDetails($id)
    {
        $entity = $this->getServiceLocator()->get('Entity\ConditionUndertaking')
            ->getById($id);

        $data = [
            'fields' => $entity
        ];

        return $this->getAdapter()->processDataForForm($data);
    }

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

        return $tableBuilder->prepareTable('lva-conditions-undertakings', $this->getTableData());
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
