<?php

/**
 * Crud table trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Crud table trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait CrudTableTrait
{
    /**
     * implementors of this trait *must* support add, edit & delete
     */
    abstract public function addAction();
    abstract public function editAction();
    abstract protected function delete();

    /**
     * Check if we have a crud action in the form table data, if so return the table data, if not return null
     *
     * @param array $formTables
     * @return array
     */
    protected function getCrudAction(array $formTables = array())
    {
        foreach ($formTables as $table) {
            if (isset($table['action'])) {
                return $table;
            }
        }

        return null;
    }

    /**
     * Redirect to the most appropriate CRUD action
     *
     * @todo within this method, we could do with calling another method to update app completion statuses
     */
    protected function handleCrudAction($data)
    {
        if (is_array($data['action'])) {
            $action = strtolower(array_keys($data['action'])[0]);
            $data['id'] = array_keys($data['action'][$action])[0];
        } else {
            $action = strtolower($data['action']);
        }

        $routeParams = array('action' => $action);

        if ($action !== 'add') {

            if (!isset($data['id'])) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')->addWarningMessage('please-select-row');
                return $this->redirect()->toRoute(null, array(), array(), true);
            }

            $routeParams['child_id'] = $data['id'];
        }

        return $this->redirect()->toRoute(null, $routeParams, array(), true);
    }

    /**
     * Once the CRUD entity has been saved, handle the necessary redirect
     *
     * @todo within this method, we could do with calling another method to update app completion statuses
     */
    protected function handlePostSave()
    {
        // we can't just opt-in to all existing route params because
        // we might have a child ID if we're editing; if so we *don't*
        // want that in the redirect or we'll end up back on the same page
        $routeParams = array(
            'id' => $this->params('id')
        );

        if ($this->isButtonPressed('addAnother')) {
            $routeParams['action'] = 'add';
        }

        return $this->redirect()->toRoute(null, $routeParams);
    }

    /**
     * Generic delete functionality; usually does the trick but
     * can be overridden if not
     *
     * @todo within this method, we could do with calling another method to update app completion statuses
     */
    public function deleteAction()
    {
        $request = $this->getRequest();

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('GenericDeleteConfirmation');

        if ($request->isPost()) {

            $this->delete();

            return $this->redirect()->toRoute(
                null,
                array('id' => $this->params('id'))
            );
        }
        return $this->render('delete', $form);
    }

    /**
     * Override built-in cancel functionality if we're
     * not on the top-level index action (i.e. we're within
     * a sub action)
     */
    protected function handleCancelRedirect($lvaId)
    {
        if ($this->params('action') !== 'index') {
            return $this->redirect()->toRoute(
                null,
                array('id' => $lvaId)
            );
        }

        // @todo We can't use parent from a trait, we need a workaround for this
        return parent::handleCancelRedirect($lvaId);
    }

    /**
     * Complete crud action
     *
     * @param string $section
     * @param string $mode
     */
    protected function completeCrudAction($section, $mode)
    {
        $this->addSuccessMessage('lva.section.' . $section . '.' . $mode . '.complete');

        if ($this->lva === 'application') {
            $this->completeApplicationCrudAction($section);
        }
    }
}
