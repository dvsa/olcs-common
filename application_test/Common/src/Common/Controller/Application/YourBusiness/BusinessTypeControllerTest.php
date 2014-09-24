<?php

/**
 * BusinessType Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Controller\Application\YourBusiness;

use CommonTest\Controller\Traits\TestBackButtonTrait;
use CommonTest\Controller\Application\AbstractApplicationControllerTestCase;
use Common\Controller\Application\Application\ApplicationController;

/**
 * BusinessType Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessTypeControllerTest extends AbstractApplicationControllerTestCase
{
    use TestBackButtonTrait;

    protected $controllerName = '\Common\Controller\Application\YourBusiness\BusinessTypeController';
    protected $defaultRestResponse = array();

    /**
     * Test indexAction
     */
    public function testIndexAction()
    {
        $this->setUpAction('index');

        $response = $this->controller->indexAction();

        // Make sure we get a view not a response
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
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

        $organisationDataBundle = array(
            'children' => array(
                'licence' => array(
                    'children' => array(
                        'organisation' => array(
                            'properties' => array(
                                'id',
                                'version',
                            ),
                            'children' => array(
                                'type' => array(
                                    'properties' => array(
                                        'id'
                                    )
                                )
                            )
                        )
                    )
                )
             )
        );

        if ($service == 'Application' && $method == 'GET' && $bundle == $organisationDataBundle) {
            return array(
                'licence' => array(
                    'organisation' => array(
                        'id' => 1,
                        'type' => array(
                            'id' => 'abc'
                        )
                    )
                )
            );
        }

        if ($service == 'ApplicationCompletion' && $method == 'GET') {

            return $this->getApplicationCompletionData();
        }
    }
}
