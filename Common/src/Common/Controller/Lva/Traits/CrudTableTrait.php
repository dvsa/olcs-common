<?php

/**
 * Crud table trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Traits;

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
    //abstract public function addAction();
    //abstract public function editAction();
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

    protected function getActionFromCrudAction($data)
    {
        if (is_array($data['action'])) {
            return strtolower(array_keys($data['action'])[0]);
        }

        return strtolower($data['action']);
    }

    /**
     * Redirect to the most appropriate CRUD action
     *
     * @param array $data
     * @return \Zend\Http\Response
     */
    protected function handleCrudAction($data)
    {
        $action = $this->getActionFromCrudAction($data);

        if (is_array($data['action'])) {
            $data['id'] = array_keys($data['action'][$action])[0];
        }

        $routeParams = array('action' => isset($data['routeAction']) ? $data['routeAction'] : $action);

        if ($action !== 'add') {

            if (!isset($data['id'])) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')->addWarningMessage('please-select-row');
                return $this->reload();
            }

            if (is_array($data['id'])) {
                $data['id'] = implode(',', $data['id']);
            }

            $routeParams['child_id'] = $data['id'];
        }

        return $this->redirect()->toRoute(null, $routeParams, array(), true);
    }

    /**
     * Once the CRUD entity has been saved, handle the necessary redirect
     */
    protected function handlePostSave()
    {
        $this->postSave($this->section);

        // we can't just opt-in to all existing route params because
        // we might have a child ID if we're editing; if so we *don't*
        // want that in the redirect or we'll end up back on the same page
        $routeParams = array(
            'id' => $this->params('id')
        );

        if ($this->isButtonPressed('addAnother')) {
            $routeParams['action'] = 'add';
        }

        // @todo maybe add a flash message in here

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
            $this->postSave($this->section);

            return $this->redirect()->toRoute(
                null,
                array('id' => $this->params('id'))
            );
        }
        return $this->render('delete', $form);
    }
}
