<?php

/**
 * Crud table trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Http\Response;

/**
 * Crud table trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait CrudTableTrait
{
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
    protected function handleCrudAction($data, $rowsNotRequired = array('add'))
    {
        $action = $this->getActionFromCrudAction($data);

        if (is_array($data['action'])) {
            $data['id'] = array_keys($data['action'][$action])[0];
        }

        $routeParams = array('action' => isset($data['routeAction']) ? $data['routeAction'] : $action);

        if (!in_array($action, $rowsNotRequired)) {

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
     *
     * @param string $prefix - if our actions aren't just 'add', 'edit', provide a prefix
     */
    protected function handlePostSave($prefix = null)
    {
        $this->postSave($this->section);

        // we can't just opt-in to all existing route params because
        // we might have a child ID if we're editing; if so we *don't*
        // want that in the redirect or we'll end up back on the same page
        $routeParams = array(
            $this->getIdentifierIndex() => $this->getIdentifier()
        );

        if ($this->isButtonPressed('addAnother')) {
            $action = $prefix !== null ? $prefix . '-' . 'add' : 'add';
            $routeParams['action'] = $action;
            $method = 'toRoute';
        } else {
            $method = 'toRouteAjax';
        }

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addSuccessMessage(
            'section.' . $this->params('action') . '.' . $this->section
        );

        return $this->redirect()->$method(null, $routeParams);
    }

    /**
     * Generic delete functionality; usually does the trick but
     * can be overridden if not
     */
    public function deleteAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $response = $this->delete();
            $this->postSave($this->section);

            if ($response instanceof Response) {
                return $response;
            }

            return $this->redirect()->toRouteAjax(
                null,
                array($this->getIdentifierIndex() => $this->getIdentifier())
            );
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('GenericDeleteConfirmation', $request);

        $params = ['sectionText' => $this->getDeleteModalMessageKey()];

        return $this->render('delete', $form, $params);
    }

    /**
     * This method needs to exists for deleteAction to work, the method should be overidden, but cannot be declared
     * abstract as it's not always required, so by default we throw an exception
     *
     * @throws \BadMethodCallException
     */
    protected function delete()
    {
        throw new \BadMethodCallException('Delete method must be implemented');
    }

    /**
     * Which translation key to use to populate the modal text.
     *
     * @return string The modal message key.
     */
    protected function getDeleteModalMessageKey()
    {
        return 'delete.confirmation.text';
    }
}
