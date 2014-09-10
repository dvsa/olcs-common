<?php

/**
 * YourBusiness Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Controller\Application\YourBusiness;

use CommonTest\Controller\Application\AbstractApplicationControllerTestCase;
use Common\Controller\Application\Application\ApplicationController;

/**
 * YourBusiness Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class YourBusinessControllerTest extends AbstractApplicationControllerTestCase
{
    protected $controllerName =  '\Common\Controller\Application\YourBusiness\YourBusinessController';

    protected $defaultRestResponse = array();

    /**
     * Test indexAction
     */
    public function testIndexAction()
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

            return $this->getApplicationCompletionData();
        }
    }
}
