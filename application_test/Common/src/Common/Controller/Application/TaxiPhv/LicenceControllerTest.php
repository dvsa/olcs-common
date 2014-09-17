<?php

/**
 * Licence Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Controller\Application\TaxiPhv;

use CommonTest\Controller\Traits\TestBackButtonTrait;
use CommonTest\Controller\Application\AbstractApplicationControllerTestCase;
use Common\Controller\Application\Application\ApplicationController;

/**
 * Licence Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceControllerTest extends AbstractApplicationControllerTestCase
{
    use TestBackButtonTrait;

    protected $controllerName =  '\Common\Controller\Application\TaxiPhv\LicenceController';

    protected $defaultRestResponse = array();

    /*
     * Determine if we already have traffic area defined for this application
     */
    protected $hasTrafficAreaDefined = true;

    /*
     * Simulate failure during adding licence
     */
    protected $notAdded = false;

    /*
     * Determine if we have a licences already added
     */
    protected $noLicences = false;

    /*
     * Mock methods for this controller
     */
    protected $mockedMethods = array(
        'getLicenceService',
        'getPostcodeService',
        'getPostcodeTrafficAreaValidator',
        'getPostcodeValidatorsChain'
    );

    public function setUpAction($action = 'index', $id = null, $data = array(), $files = array())
    {
        parent::setUpAction($action, $id, $data, $files);

        $mockLicenceService = $this->getMock('\StdClass', array('generateLicence'));

        $mockLicenceService->expects($this->any())
            ->method('generateLicence')
            ->will($this->returnValue(1));

        $this->controller->expects($this->any())
            ->method('getLicenceService')
            ->will($this->returnValue($mockLicenceService));

        $mockPostcodeValidatorsChain = $this->getMock('\StdClass', array('attach'));
        $mockPostcodeValidatorsChain->expects($this->any())
            ->method('attach')
            ->will($this->returnValue(true));

        $this->controller->expects($this->any())
            ->method('getPostcodeValidatorsChain')
            ->will($this->returnValue($mockPostcodeValidatorsChain));

        $mockPostcodeValidator = $this->getMock(
            '\Common\Form\Elements\Validators\PrivateHireLicenceTrafficAreaValidator',
            array('isValid', 'setPrivateHireLicencesCount', 'setTrafficArea')
        );

        $mockPostcodeValidator->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->controller->expects($this->any())
            ->method('getPostcodeTrafficAreaValidator')
            ->will($this->returnValue($mockPostcodeValidator));

        $mockPostcodeService = $this->getMock('\StdClass', array('getTrafficAreaByPostcode'));

        $mockPostcodeService->expects($this->any())
            ->method('getTrafficAreaByPostcode')
            ->will(
                $this->returnValueMap(
                    array(
                        array('LS1 4ES', array('B', 'North East of England')),
                        array('BT1 4EE', array('N', 'Northern Ireland')),
                    )
                )
            );

        $this->controller->expects($this->any())
            ->method('getPostcodeService')
            ->will($this->returnValue($mockPostcodeService));

    }

    /**
     * Test indexAction
     * @group acurrent
     */
    public function testIndexAction()
    {
        $this->setUpAction('index');

        $response = $this->controller->indexAction();

        // Make sure we get a view not a response
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test indexAction With submit
     * @group acurrent
     */
    public function testIndexActionWithSubmitWithRows()
    {
        $this->setUpAction(
            'index',
            null,
            array(
                'table' => array(
                    'rows' => 1
                )
            )
        );

        $this->controller->setEnabledCsrf(false);

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction With submit
     * @group acurrent
     */
    public function testIndexActionWithSubmitWithoutRows()
    {
        $this->setUpAction(
            'index',
            null,
            array(
                'table' => array(
                    'rows' => 0
                )
            )
        );

        $this->controller->setEnabledCsrf(false);

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test indexAction With Add Crud Action
     * @group acurrent
     */
    public function testIndexActionWithAddCrudAction()
    {
        $this->setUpAction(
            'index', null, array(
                'table' => array(
                    'rows' => 0,
                    'action' => 'Add'
                )
            )
        );

        $this->controller->setEnabledCsrf(false);

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction With Edit Crud Action without id
     * @group acurrent
     */
    public function testIndexActionWithEditCrudActionWithoutId()
    {
        $this->setUpAction(
            'index', null, array(
                'table' => array(
                    'rows' => 1,
                    'action' => 'Edit'
                )
            )
        );

        $this->controller->setEnabledCsrf(false);

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction With Edit Crud Action
     * @group acurrent
     */
    public function testIndexActionWithEditCrudAction()
    {
        $this->setUpAction(
            'index', null, array(
                'table' => array(
                    'rows' => 1,
                    'action' => 'Edit',
                    'id' => 2
                ),
                'id' => 2
            )
        );

        $this->controller->setEnabledCsrf(false);

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction With Edit Link Crud action
     * @group acurrent
     */
    public function testIndexActionWithEditLinkCrudAction()
    {
        $this->setUpAction(
            'index', null, array(
                'table' => array(
                    'rows' => 1,
                    'action' => array('edit' => array('2' => 'String')),
                    'id' => 2
                )
            )
        );

        $this->controller->setEnabledCsrf(false);

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test addAction
     * @group acurrent
     */
    public function testAddAction()
    {
        $this->setUpAction('add');

        $response = $this->controller->addAction();

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test addAction with cancel
     * @group acurrent
     */
    public function testAddActionWithCancel()
    {
        $post = array(
            'form-actions' => array(
                'cancel' => 'Cancel'
            )
        );

        $this->setUpAction('add', null, $post);

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->addAction();

        $this->assertInstanceOf('\Zend\Http\Response', $response);
    }

    /**
     * Test addAction with submit
     * @group acurrent
     */
    public function testAddActionWithSubmit()
    {
        $this->setUpAction(
            'add',
            null,
            array(
                'data' => array(
                    'id' => '',
                    'version' => '',
                    'privateHireLicenceNo' => 'AB12345',
                    'licence' => 1
                ),
                'contactDetails' => array(
                    'id' => '',
                    'version' => '',
                    'description' => 'Some Council',
                ),
                'address' => array(
                    'id' => '',
                    'version' => '',
                    'addressLine1' => 'Address 1',
                    'town' => 'City',
                    'countryCode' => 'GB',
                    'postcode' => 'AB1 1BA'
                )
            )
        );

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->addAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test addAction with submit
     *
     * @group acurrent
     * @expectedException Exception
     */
    public function testAddActionWithSubmitWithFailedContactDetails()
    {
        $this->setUpAction(
            'add',
            null,
            array(
                'data' => array(
                    'id' => '',
                    'version' => '',
                    'privateHireLicenceNo' => 'AB12345',
                    'licence' => 1
                ),
                'contactDetails' => array(
                    'id' => '',
                    'version' => '',
                    'description' => 'Some Council',
                ),
                'address' => array(
                    'id' => '',
                    'version' => '',
                    'addressLine1' => 'Address 1',
                    'town' => 'City',
                    'countryCode' => 'GB',
                    'postcode' => 'AB1 1BA'
                )
            )
        );

        $this->setRestResponse('ContactDetails', 'POST', array());

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->addAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test addAction with submit and failure
     * @expectedException \Exception
     * @group acurrent
     */
    public function testAddActionWithSubmitAndFailure()
    {
        $this->setUpAction(
            'add',
            null,
            array(
                'data' => array(
                    'id' => '',
                    'version' => '',
                    'privateHireLicenceNo' => 'AB12345',
                    'licence' => 1
                ),
                'contactDetails' => array(
                    'id' => '',
                    'version' => '',
                    'description' => 'Some Council',
                ),
                'address' => array(
                    'id' => '',
                    'version' => '',
                    'addressLine1' => 'Address 1',
                    'town' => 'City',
                    'countryCode' => 'GB',
                    'postcode' => 'LS1 4ES'
                )
            )
        );
        $this->notAdded = true;
        $this->controller->setEnabledCsrf(false);
        $this->controller->addAction();

    }

    /**
     * Test editAction with cancel
     * @group acurrent
     */
    public function testEditActionWithCancel()
    {
        $post = array(
            'form-actions' => array(
                'cancel' => 'Cancel'
            )
        );

        $this->setUpAction('edit', 1, $post);

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->editAction();

        $this->assertInstanceOf('\Zend\Http\Response', $response);
    }

    /**
     * Test editAction
     * @group acurrent
     */
    public function testEditAction()
    {
        $this->setUpAction('edit', 1, array('id' => 1));
        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->editAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test edit action with submit
     * @group acurrent
     */
    public function testEditActionWithSubmit()
    {
        $this->setUpAction(
            'edit',
            null,
            array(
                'data' => array(
                    'id' => '1',
                    'version' => '1',
                    'privateHireLicenceNo' => 'AB12345',
                    'licence' => 1
                ),
                'contactDetails' => array(
                    'id' => '1',
                    'version' => '1',
                    'description' => 'Some Council',
                ),
                'address' => array(
                    'id' => '1',
                    'version' => '1',
                    'addressLine1' => 'Address 1',
                    'town' => 'City',
                    'countryCode' => 'GB',
                    'postcode' => 'LS1 4ES'
                ),
                'dataTrafficArea' => array(
                    'trafficArea' => 'B'
                )
            )
        );

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->editAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction With submit with rows and traffic area
     * @group acurrent
     */
    public function testIndexActionWithSubmitWithRowsAndTrafficArea()
    {
        $this->setUpAction(
            'index',
            null,
            array(
                'table' => array(
                    'rows' => 1
                ),
                'dataTrafficArea' => array(
                    'trafficArea' => 'B'
                )
            )
        );
        $this->hasTrafficAreaDefined = false;
        $this->controller->setEnabledCsrf(false);

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test edit action with submit
     * @group acurrent
     */
    public function testEditActionWithSubmitAndTrafficAreaDefined()
    {
        $this->setUpAction(
            'edit',
            null,
            array(
                'data' => array(
                    'id' => '1',
                    'version' => '1',
                    'privateHireLicenceNo' => 'AB12345',
                    'licence' => 1
                ),
                'contactDetails' => array(
                    'id' => '1',
                    'version' => '1',
                    'description' => 'Some Council',
                ),
                'address' => array(
                    'id' => '1',
                    'version' => '1',
                    'addressLine1' => 'Address 1',
                    'town' => 'City',
                    'countryCode' => 'GB',
                    'postcode' => 'LS1 4ES'
                ),
                'dataTrafficArea' => array(
                    'trafficArea' => 'B'
                )
            )
        );

        $this->controller->setEnabledCsrf(false);
        $this->hasTrafficAreaDefined = true;
        $response = $this->controller->editAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction with no licence
     * @group acurrent
     */
    public function testIndexActionNoLicences()
    {
        $this->setUpAction('index');

        $this->noLicences = true;
        $this->hasTrafficAreaDefined = false;
        $response = $this->controller->indexAction();

        // Make sure we get a view not a response
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test editAction with no traffic area
     * @group acurrent
     */
    public function testEditActionWithNoTrafficArea()
    {
        $this->setUpAction('edit', 1);
        $this->controller->setEnabledCsrf(false);
        $this->hasTrafficAreaDefined = false;
        $response = $this->controller->editAction();

        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test indexAction With Add Crud Action no traffic area
     * @group acurrent
     */
    public function testIndexActionWithAddCrudActionWithNoTrafficArea()
    {
        $this->setUpAction(
            'index', null, array(
                'table' => array(
                    'rows' => 0,
                    'action' => 'Add'
                )
            )
        );

        $this->controller->setEnabledCsrf(false);
        $this->hasTrafficAreaDefined = false;
        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction With Add Crud Action and no traffic area defined but selected
     * @group acurrent
     */
    public function testIndexActionWithAddCrudActionWithNoTrafficAreaAndButSelected()
    {
        $this->setUpAction(
            'index', null, array(
                'table' => array(
                    'rows' => 0,
                    'action' => 'Add'
                ),
                'dataTrafficArea' => array(
                    'trafficArea' => 'B'
                )
            )
        );

        $this->controller->setEnabledCsrf(false);
        $this->hasTrafficAreaDefined = false;
        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test deleteAction
     * @group acurrent
     */
    public function testDeleteAction()
    {
        $this->setUpAction('delete', 1);

        $response = $this->controller->deleteAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test deleteAction without id
     * @group acurrent
     */
    public function testDeleteActionWithoutId()
    {
        $this->setUpAction('delete');

        $response = $this->controller->deleteAction();

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
            return $this->getLicenceData('psv', 'ltyp_sr');
        }

        $appWithTrafficAreaBundle = array(
            'properties' => array(
                'id',
                'version',
            ),
            'children' => array(
                'licence' => array(
                    'properties' => array(
                        'id'
                    ),
                    'children' => array(
                        'trafficArea' => array(
                            'properties' => array(
                                'id',
                                'name'
                            )
                        )
                    )
                )
            )
        );
        if ($service == 'Application' && $method == 'GET' && $bundle == $appWithTrafficAreaBundle) {
            if ($this->hasTrafficAreaDefined) {
                return array(
                    'id' => 1,
                    'version' => 1,
                    'licence' => array(
                        'id' => 1,
                        'trafficArea' => array(
                            'id' => 'B',
                            'name' => 'North East of England'
                        )
                    )
                );
            } else {
                return array(
                    'id' => 1,
                    'version' => 1,
                    'licence' => array(
                        'id' => 1,
                        'trafficArea' => null
                    )
                );
            }
        }

        $licenceBundle = array(
            'properties' => array(
                'id',
                'version'
            ),
            'children' => array(
                'licence' => array(
                    'properties' => array(
                        'id',
                        'version'
                    )
                )
            )
        );
        if ($service == 'Application' && $method == 'GET' && $bundle == $licenceBundle) {
            return array(
                'id' => 1,
                'version' => 1,
                'licence' => array(
                    'id' => 1,
                    'version' => 1
                )
            );
        }

        if ($service == 'ApplicationCompletion' && $method == 'GET') {

            return $this->getApplicationCompletionData();
        }

        if ($service == 'ContactDetails' && $method == 'POST') {
            return array(
                'id' => 1
            );
        }

        $countBundle = array(
            'properties' => array(
                'id',
                'version'
            )
        );
        if ($service == 'PrivateHireLicence' && $method == 'GET' && $countBundle == $bundle) {
            return array(
                'Results' => array(
                    array(
                        'id' => 1,
                        'version' => 1
                    )
                ),
                'Count' => 1
            );
        }

        if ($service == 'PrivateHireLicence' && $method == 'POST') {
            if (!$this->notAdded) {
                return array(
                    'id' => 1
                );
            } else {
                return array();
            }
        }

        if ($service == 'PrivateHireLicence' && $method == 'PUT') {
            return array(
                'id' => 1
            );
        }

        $fullBundle = array(
            'properties' => array(
                'id',
                'version',
                'privateHireLicenceNo',
            ),
            'children' => array(
                'contactDetails' => array(
                    'properties' => array(
                        'id',
                        'version',
                        'description'
                    ),
                    'children' => array(
                        'address' => array(
                            'properties' => array(
                                'id',
                                'version',
                                'addressLine1',
                                'addressLine2',
                                'addressLine3',
                                'addressLine4',
                                'postcode',
                                'town'
                            ),
                            'children' => array(
                                'countryCode' => array(
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
        $phlEditData = array(
            'id' => 1
        );
        if ($service == 'PrivateHireLicence' && $method == 'GET' && $fullBundle == $bundle && $data == $phlEditData) {
            return array(
                'id' => 1,
                'version' => 1,
                'privateHireLicenceNo' => 'AB12345',
                'contactDetails' => array(
                    'id' => 2,
                    'version' => 2,
                    'description' => 'DMBC',
                    'address' => array(
                        'id' => 3,
                        'version' => 3,
                        'addressLine1' => '1 Test Street',
                        'addressLine2' => 'Testtown',
                        'addressLine3' => '',
                        'addressLine4' => '',
                        'postcode' => 'AB12 1AB',
                        'town' => 'Doncaster',
                        'countryCode' => array(
                            'id' => 'GB'
                        )
                    )
                )
            );
        }
        if ($service == 'PrivateHireLicence' && $method == 'GET' && $fullBundle == $bundle) {
            if (!$this->noLicences) {
                return array(
                    'Results' => array(
                        array(
                            'id' => 1,
                            'version' => 1,
                            'privateHireLicenceNo' => 'AB12345',
                            'contactDetails' => array(
                                'id' => 2,
                                'version' => 2,
                                'description' => 'DMBC',
                                'address' => array(
                                    'id' => 3,
                                    'version' => 3,
                                    'addressLine1' => '1 Test Street',
                                    'addressLine2' => 'Testtown',
                                    'addressLine3' => '',
                                    'addressLine4' => '',
                                    'postcode' => 'AB12 1AB',
                                    'town' => 'Doncaster',
                                    'countryCode' => array(
                                        'id' => 'GB'
                                    )
                                )
                            )
                        )
                    )
                );
            } else {
                return array(
                    'Results' => array(),
                    'Count' => 0
                );
            }
        }

        $trafficAreaBundle = array(
            'properties' => array(
                'id',
                'name',
            ),
        );
        if ($service == 'TrafficArea' && $method == 'GET' && $bundle == $trafficAreaBundle) {
            return array(
                'Count' => 2,
                'Results' => array(
                    array(
                        'id' => 'B',
                        'name' => 'North East of England'
                    ),
                    array(
                        'id' => 'K',
                        'name' => 'London and the South East of England'
                    ),
                    array(
                        'id' => 'N',
                        'name' => 'Northern ireland'
                    ),
                )
            );
        }
    }
}
