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
     */
    protected function handleCrudAction($data)
    {
        $action = strtolower($data['action']);

        if ($action === 'add') {
            $routeParams = array(
                'action' => 'add'
            );
        } else {
            $routeParams = array(
                'action' => $action,
                'child_id' => $data['id']
            );
        }

        return $this->redirect()->toRoute(
            null,
            $routeParams,
            array(),
            true
        );
    }

    /**
     * Once the CRUD entity has been saved, handle the necessary redirect
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
}
