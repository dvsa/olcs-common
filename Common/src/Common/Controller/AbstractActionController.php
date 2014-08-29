<?php

/**
 * An abstract controller that all ordinary OLCS controllers inherit from
 *
 * @author Pelle Wessman <pelle.wessman@valtech.se>
 * @author Michael Cooperr <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Nick Payne <nick.payne@valtech.co.uk>
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
 * @author Nick Payne <nick.payne@valtech.co.uk>
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
     * The current page's main title
     *
     * @var string
     */
    protected $pageTitle = null;

    /**
     * The current page's sub title, if applicable
     *
     * @var string
     */
    protected $pageSubTitle = null;

    /**
     * The current page's extra layout, over and above the
     * standard base template
     *
     * @var string
     */
    protected $pageLayout = null;

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
     * @codeCoverageIgnore
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function setupIndexRoute(\Zend\Mvc\MvcEvent $e)
    {
        if (empty($this->indexRoute)) {
            $this->indexRoute = [
                $e->getRouteMatch()->getMatchedRouteName(),
                array_merge($this->params()->fromRoute(), ['action' => 'index', 'id' => null])
            ];
        }
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
     * @codeCoverageIgnore
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
            if ($page) {
                $page->setParams($routeParams);
            }
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

    /**
     * Get param from post
     *
     * @param string $name
     * @return string
     */
    public function getFromPost($name)
    {
        return $this->params()->fromPost($name);
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

    /**
     * Wrapper method to render a view with optional title and sub title values
     *
     * @param string|ViewModel $view
     * @param string $pageTitle
     * @param string $pageSubTitle
     *
     * @return ViewModel
     */
    protected function renderView($view, $pageTitle = null, $pageSubTitle = null)
    {
        // allow for very simple views to be passed as a string. Obviously this
        // precludes the passing of any template variables but can still come
        // in handy when no extra variables need to be set
        if (is_string($view)) {
            $viewName = $view;
            $view = new ViewModel();
            $view->setTemplate($viewName);
        }

        // no, I don't know why it's not getTerminal or isTerminal either...
        if ($view->terminate()) {
            return $view;
        }

        // allow both the page title and sub title to be passed as explicit
        // arguments to this method
        if ($pageTitle !== null) {
            $this->pageTitle = $pageTitle;
        }

        if ($pageSubTitle !== null) {
            $this->pageSubTitle = $pageSubTitle;
        }

        // every page has a header, so no conditional logic needed here
        $header = new ViewModel(
            [
                'pageTitle' => $this->pageTitle,
                'pageSubTitle' => $this->pageSubTitle
            ]
        );
        $header->setTemplate('layout/partials/header');

        // allow a controller to specify a more specific page layout to use
        // in addition to the base one all views inherit from
        if ($this->pageLayout !== null) {
            $layout = $this->pageLayout;
            if (is_string($layout)) {
                $viewName = $layout;
                $layout = new ViewModel();
                $layout->setTemplate('layout/' . $viewName);
                $layout->setVariables($view->getVariables());
            }

            $layout->addChild($view, 'content');

            // reassign the main view to be this new layout so that when we
            // come to create the base view it can just add '$view' without
            // having to care what it is
            $view = $layout;
        }

        // we *always* inherit from the same base layout
        $base = new ViewModel();
        $base->setTemplate('layout/base')
            ->setTerminal(true)
            ->setVariables($view->getVariables())
            ->addChild($header, 'header')
            ->addChild($view, 'content');

        return $base;
    }

    /*
     * Load an array of script files which will be rendered inline inside a view
     *
     * @param array $scripts
     * @return array
     */
    protected function loadScripts($scripts)
    {
        return $this->getServiceLocator()->get('Script')->loadFiles($scripts);
    }
}
