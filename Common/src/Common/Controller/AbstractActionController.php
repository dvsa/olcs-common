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

    private $loggedInUser;

    /**
     * onDispatch now populates this with the route for the index of
     * the controller curently being executed.
     *
     * @var array
     */
    protected $indexRoute = [];

    /**
     * @codeCoverageIgnore
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->setupIndexRoute($e);

        $this->preOnDispatch();
        parent::onDispatch($e);
    }

    /**
     * Sets up the index route array.
     *
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function setupIndexRoute(\Zend\Mvc\MvcEvent $e)
    {
        $this->indexRoute = [
            $e->getRouteMatch()->getMatchedRouteName(),
            array_merge($this->params()->fromRoute(), ['action' => 'index'])
        ];
    }

    /**
     * Olcs specific onDispatch method.
     */
    public function preOnDispatch()
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();

        $headers->addHeaderLine('Cache-Control', 'no-cache, must-revalidate');
        $headers->addHeaderLine('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');

        $this->setLoggedInUser(1);
    }

    /**
     * Does what it says on the tin.
     *
     * @return mixed
     */
    public function redirectToIndex()
    {
        return call_user_func_array([$this->redirect(), 'toRoute'], $this->indexRoute);
    }

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
    public function getParams($keys)
    {
        $params = [];
        $getParams = $this->getAllParams();
        foreach ($getParams as $key => $value) {
            if (in_array($key, $keys)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }

    public function getAllParams()
    {
        $getParams = array_merge(
            $this->getEvent()->getRouteMatch()->getParams(),
            $this->getRequest()->getQuery()->toArray()
        );

        return $getParams;
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
    public function checkForCrudAction($route = null, $params = array(), $itemIdParam = 'id')
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

        return $this->redirect()->toRoute($route, $params, [], true);
    }

    /**
     * Called when a crud action is missing a required ID
     */
    protected function crudActionMissingId()
    {
        $this->addWarningMessage('Please select a row');
        return $this->redirectToRoute(null, array(), array(), true);
    }

    /**
     * Build a table from config and results
     *
     * @param string $table
     * @param array $results
     * @param array $data
     * @return string
     */
    public function buildTable($table, $results, $data = array())
    {
        return $this->getTable($table, $results, $data, true);
    }

    /**
     * Build a table from config and results, and return the table object
     *
     * @param string $table
     * @param array $results
     * @param array $data
     * @param boolean $render
     * @return string
     */
    public function getTable($table, $results, $data = array(), $render = false)
    {
        if (!isset($data['url'])) {
            $data['url'] = $this->getPluginManager()->get('url');
        }

        return $this->getServiceLocator()->get('Table')->buildTable($table, $results, $data, $render);
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

    /**
     * Get url from route
     *
     * @param string $route
     * @return string
     */
    public function getUrlFromRoute($route, $params = array())
    {
        return $this->url()->fromRoute($route, $params);
    }

    /**
     * Wraps the redirect()->toRoute to help with unit testing
     *
     * @param string $route
     * @param array $params
     * @param array $options
     * @param bool $reuse
     * @return \Zend\Http\Response
     */
    public function redirectToRoute($route = null, $params = array(), $options = array(), $reuse = false)
    {
        return $this->redirect()->toRoute($route, $params, $options, $reuse);
    }

    /**
     * Get param from route
     *
     * @param string $name
     * @return string
     */
    public function getFromRoute($name)
    {
        return $this->params()->fromRoute($name);
    }

    public function getLoggedInUser()
    {
        return $this->loggedInUser;
    }

    public function setLoggedInUser($id)
    {
        $this->loggedInUser = $id;
        return $this;
    }

    /**
     * Get uploader
     *
     * @return object
     */
    public function getUploader()
    {
        return $this->getServiceLocator()->get('FileUploader')->getUploader();
    }
}
