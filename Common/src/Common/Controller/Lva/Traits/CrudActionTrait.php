<?php

/**
 * Crud action trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Crud action trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait CrudActionTrait
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
    protected function handleCrudAction(
        $data,
        $rowsNotRequired = ['add'],
        $childIdParamName = 'child_id',
        $route = null
    ) {
        $action = $this->getActionFromCrudAction($data);

        if (is_array($data['action'])) {
            $data['id'] = array_keys($data['action'][$action])[0];
        }

        $routeParams = array('action' => isset($data['routeAction']) ? $data['routeAction'] : $action);

        if (!in_array($action, $rowsNotRequired)) {

            if (!isset($data['id'])) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')->addWarningMessage('please-select-row');
                return $this->redirect()->refresh();
            }

            if (is_array($data['id'])) {
                $data['id'] = implode(',', $data['id']);
            }

            $routeParams[$childIdParamName] = $data['id'];
        }
        $options = ['query' => $this->getRequest()->getQuery()->toArray()];

        return $this->redirect()->toRoute($route, $routeParams, $options, true);
    }
}
