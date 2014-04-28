<?php

/**
 * An abstract controller that all ordinary OLCS controllers inherit from
 *
 * @author Pelle Wessman <pelle.wessman@valtech.se>
 * @author Michael Cooperr <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Controller;

use Common\Util;
use Zend\View\Model\ViewModel;

/**
 * An abstract controller that all ordinary OLCS controllers inherit from
 *
 * @author Pelle Wessman <pelle.wessman@valtech.se>
 * @author Michael Cooperr <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractActionController extends \Zend\Mvc\Controller\AbstractActionController
{
    use Util\LoggerTrait,
        Util\FlashMessengerTrait,
        Util\RestCallTrait;

    /**
     * Set navigation for breadcrumb
     * @param type $label
     * @param type $params
     */
    public function setBreadcrumb($navRoutes = array())
    {
        foreach ($navRoutes as $route => $routeParams) {
            $navigation = $this->getServiceLocator()->get('navigation');
            $page = $navigation->findBy('route', $route);
            $page->setParams($routeParams);
        }
    }

    /**
     * Get all request params from the query string and route and send back the required ones
     * @param type $keys
     * @return type
     */
    protected function getParams($keys)
    {
        $params = [];
        $getParams = array_merge(
            $this->getEvent()->getRouteMatch()->getParams(),
            $this->getRequest()->getQuery()->toArray()
        );
        foreach ($getParams as $key => $value) {
            if (in_array($key, $keys)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }

    /**
     * Gets a from from either a built or custom form config.
     * @param type $type
     * @return type
     */
    protected function getForm($type)
    {
        $form = $this->getServiceLocator()->get('OlcsCustomForm')->createForm($type);
        return $form;
    }

    protected function getFormGenerator()
    {
        return $this->getServiceLocator()->get('OlcsCustomForm');
    }

    /**
     * Method to process posted form data and validate it and process a callback
     * @param type $form
     * @param type $callback
     * @return \Zend\Form
     */
    protected function formPost($form, $callback, $additionalParams = array())
    {
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $validatedData = $form->getData();
                $params = [
                    'validData' => $validatedData,
                    'form' => $form,
                    'params' => $additionalParams
                ];
                if (is_callable($callback)) {
                    $callback($params);
                }

                call_user_func_array(array($this, $callback), $params);
            }
        }
        return $form;
    }

    /**
     * Generate a form with a callback
     *
     * @param string $name
     * @param callable $callback
     * @return object
     */
    protected function generateForm($name, $callback)
    {
        $form = $this->getForm($name);

        return $this->formPost($form, $callback);
    }

    /**
     * Generate a form with data
     *
     * @param string $name
     * @param callable $callback
     * @param mixed $data
     * @return object
     */
    protected function generateFormWithData($name, $callback, $data = null)
    {
        $form = $this->generateForm($name, $callback);

        if (is_array($data)) {
            $form->setData($data);
        }

        return $form;
    }

    /**
     * Generate form from GET call
     *
     * @todo Need to do something with $return to format the data
     *
     * @param string $name
     * @param callable $callback
     * @param string $service
     * @param int $id
     *
     * @return object
     */
    protected function generateFormFromGet($name, $callback, $service, $id)
    {
        $return = $this->makeRestCall($service, 'GET', array('id' => $id));

        return $this->generateFormWithData($name, $callback, $return);
    }

    /**
     * Check for crud actions
     *
     * @param string $route
     * @param array $params
     * @param string $itemIdParam
     *
     * @return boolean
     */
    protected function checkForCrudAction($route = null, $params = array(), $itemIdParam = 'id')
    {
        $action = $this->params()->fromPost('action');

        if (empty($action)) {
            return false;
        }

        $action = strtolower($action);

        $params = array_merge($params, array('action' => $action));

        if ($action !== 'add') {

            $id = $this->params()->fromPost('id');

            if (empty($id)) {

                $this->crudActionMissingId();
                return false;
            }

            $params[$itemIdParam] = $id;
        }

        $this->redirect()->toRoute($route, $params, [], true);
    }

    /**
     * Called when a crud action is missing a required ID
     */
    protected function crudActionMissingId()
    {
        $this->addErrorMessage('Please select a row first');
        $this->redirect()->toRoute(null, array(), array(), true);
    }

    /*
     * Build a table from config and results
     *
     * @param string $table
     * @param array $results
     * @param array $data
     * @return string
     */
    public function buildTable($table, $results, $data = array())
    {
        if (!isset($data['url'])) {
            $data['url'] = $this->getPluginManager()->get('url');
        }

        return $this->getServiceLocator()->get('Table')->buildTable($table, $results, $data);
    }

    /**
     * Return a new view model
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function getViewModel($params = array())
    {
        return new ViewModel($params);
    }
}
