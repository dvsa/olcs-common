<?php

/**
 * Abstract Section Controller TestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller;

use OlcsTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;
use Zend\View\Model\ViewModel;

/**
 * Abstract Section Controller TestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractSectionControllerTestCase extends PHPUnit_Framework_TestCase
{
    protected $controllerName = '';
    protected $defaultRestResponse = array();
    protected $restResponses = array();
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $routeName;
    protected $event;
    protected $mockedMethods = array();
    protected $additionalMockedMethods = array();
    protected $identifierName = '';
    protected $serviceManager;
    protected $mockSectionServiceFactory;

    /**
     * Setup an action
     *
     * @param string $action
     * @param int $id
     * @param array $data
     */
    protected function setUpAction($action = 'index', $id = null, $data = array(), $files = array())
    {
        $this->tearDown();

        $methods = array_merge($this->mockedMethods, $this->additionalMockedMethods, array('makeRestCall'));

        $this->controller = $this->getMock($this->controllerName, $methods);

        $this->controller->expects($this->any())
            ->method('makeRestCall')
            ->will($this->returnCallback(array($this, 'mockRestCall')));

        $this->serviceManager = Bootstrap::getServiceManager();
        $this->serviceManager->setAllowOverride(true);
        $licenceService = $this->getMock('Olcs\Service\Data\Licence');
        $licenceService->expects($this->any())->method('fetchLicenceData')->willReturn(
            array(
                'id' => 37,
                'goodsOrPsv' => array('id' => 'lcat_psv', 'description' => 'Goods'),
                'licenceType' => array('id' => 'ltyp_bus','description'=> 'Type'),
                'status' => array('description'=> 'All good'),
                'licNo' => '1234',
            )
        );

        $this->mockSectionServiceFactory = $this->getMock(
            '\stdClass',
            array('getSectionService', 'createSectionService')
        );

        $this->serviceManager->setService('Olcs\Service\Data\Licence', $licenceService);
        $this->serviceManager->setService('SectionService', $this->mockSectionServiceFactory);

        $this->request = new Request();
        $this->response = new Response();

        $routeMatch = array(
            'controller' => trim($this->controllerName, '\\'),
            'action' => $action,
            $this->identifierName => 1
        );

        if (is_array($id)) {
            $query = new \Zend\Stdlib\Parameters(array('id' => $id));

            $this->controller->getRequest()->setMethod('get')->setQuery($query);
        } else {
            $routeMatch['id'] = $id;
        }

        $this->routeMatch = new RouteMatch($routeMatch);

        $this->routeMatch->setMatchedRouteName($this->routeName);

        $this->event = new MvcEvent();
        $config = $this->serviceManager->get('Config');

        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->event->setRequest($this->request);
        $this->event->setResponse($this->response);

        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($this->serviceManager);

        if (!empty($data)) {

            $post = new \Zend\Stdlib\Parameters($data);

            $this->controller->getRequest()->setMethod('post')->setPost($post);
        }

        if (!empty($files)) {

            $files = new \Zend\Stdlib\Parameters($files);

            $this->controller->getRequest()->setFiles($files);
        }
    }

    /**
     * Reset all
     */
    protected function tearDown()
    {
        $this->controller = null;
        $this->request = null;
        $this->routeMatch = null;
        $this->event = null;
        $this->restResponses = array();
        foreach ($this->defaultRestResponse as $service => $details) {
            foreach ($details as $method => $response) {
                $this->restResponses[$service][$method]['default'] = $response;
            }
        }
    }

    /**
     * Override a rest response
     *
     * @param string $service
     * @param string $method
     * @param mixed $response
     */
    protected function setRestResponse($service, $method, $response = null, $bundle = array())
    {
        if (!empty($bundle)) {
            $which = base64_encode(json_encode($bundle));
        } else {
            $which = 'default';
        }

        $this->restResponses[$service][$method][$which] = $response;
    }

    /**
     * Mock the rest call
     *
     * @param string $service
     * @param string $method
     * @param array $data
     * @param array $bundle
     */
    public function mockRestCall($service, $method, $data = array(), $bundle = array())
    {
        if ($method == 'PUT' || $method == 'DELETE') {
            return null;
        }

        if (!empty($bundle)) {
            $which = base64_encode(json_encode($bundle));
        } else {
            $which = 'default';
        }

        if (isset($this->restResponses[$service][$method][$which])) {

            return $this->restResponses[$service][$method][$which];

        } elseif (isset($this->restResponses[$service][$method]['default'])) {

            return $this->restResponses[$service][$method]['default'];
        }

        $response = $this->mockRestCalls($service, $method, $data, $bundle);

        if ($method == 'GET' && $response === null) {
            $this->fail('Missed a mocked rest call: Service - ' . $service . ' Bundle - ' . print_r($bundle, true));
        }

        return $response;
    }

    /**
     * Abstract mock rest calls method
     *
     * @param string $service
     * @param string $method
     * @param array $data
     * @param array $bundle
     */
    abstract protected function mockRestCalls($service, $method, $data, $bundle);

    /**
     * Get form from response
     *
     * @param \Zend\View\Model\ViewModel $view
     * @return \Zend\Form\Form
     */
    protected function getFormFromView($view)
    {
        if ($view instanceof ViewModel) {

            $main = $this->getMainView($view);

            return $main->getVariable('form');
        }

        $this->fail('Trying to get form of a Response object instead of a ViewModel');
    }

    /**
     * Get table from response
     *
     * @param \Zend\View\Model\ViewModel $view
     * @return \Zend\Form\Form
     */
    protected function getTableFromView($view)
    {
        if ($view instanceof ViewModel) {

            $main = $this->getMainView($view);

            return $main->getVariable('table');
        }

        $this->fail('Trying to get table of a Response object instead of a ViewModel');
    }

    /**
     * Get main view
     *
     * @param \CommonTest\Controller\ViewModel $view
     * @return ViewModel
     */
    protected function getMainView($view)
    {
        if ($view instanceof ViewModel) {

            $mainChildren = $view->getChildrenByCaptureTo('main');
            $this->assertEquals(1, count($mainChildren));

            return $mainChildren[0];
        }

        $this->fail('Trying to get main child of a Response object instead of a ViewModel');
    }
}
