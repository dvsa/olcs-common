<?php

/**
 * Application Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Controller\Application\Application;

use CommonTest\Controller\Application\AbstractApplicationControllerTestCase;
use Common\Controller\Application\Application\ApplicationController;

/**
 * Application Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationControllerTest extends AbstractApplicationControllerTestCase
{
    protected $controllerName =  '\Common\Controller\Application\Application\ApplicationController';

    protected $defaultRestResponse = array();

    private $lastSection = null;

    /**
     * Test that getNamespaceParts does what is expected
     */
    public function testGetNamespaceParts()
    {
        $controller = new ApplicationController();
        $parts = $controller->getNamespaceParts();

        $expected = array(
            'ApplicationController',
            'Application',
            'Application',
            'Controller',
            'Common',
        );

        $this->assertEquals($expected, $parts);
    }

    /**
     * Test indexAction
     */
    public function testIndexAction()
    {
        $this->setUpAction('index');

        $this->lastSection = 'Application/YourBusiness/BusinessDetails';

        $response = $this->controller->indexAction();

        // Make sure we get a view not a response
        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction without last section
     */
    public function testIndexActionWithoutLastSection()
    {
        $this->setUpAction('index');

        $response = $this->controller->indexAction();

        // Make sure we get a view not a response
        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Mock the rest call
     *
     * @param string $service
     * @param string $method
     * @param array $data
     * @param array $bundle
     */
    protected function mockRestCalls($service, $method, $data = array(), $bundle = array())
    {
        if ($service == 'Application' && $method == 'GET'
            && $bundle == ApplicationController::$applicationLicenceDataBundle) {

            return $this->getLicenceData('goods');
        }

        if ($service == 'ApplicationCompletion' && $method == 'GET') {

            return $this->getApplicationCompletionData($this->lastSection);
        }
    }
}
