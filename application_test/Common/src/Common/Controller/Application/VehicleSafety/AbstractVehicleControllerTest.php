<?php

/**
 * AbstractVehicleControllerTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Application\VehicleSafety;

use CommonTest\Controller\Traits\TestBackButtonTrait;
use CommonTest\Controller\Application\AbstractApplicationControllerTestCase;
use Common\Controller\Application\Application\ApplicationController;

/**
 * AbstractVehicleControllerTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVehicleControllerTest extends AbstractApplicationControllerTestCase
{
    use TestBackButtonTrait;

    protected $otherLicencesBundle = array(
        'properties' => array(),
        'children' => array(
            'licenceVehicles' => array(
                'properties' => array(),
                'children' => array(
                    'licence' => array(
                        'properties' => array(
                            'id',
                            'licNo'
                        ),
                        'children' => array(
                            'applications' => array(
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

    protected $applicationStatusBundle = array(
        'properties' => array(),
        'children' => array(
            'status' => array(
                'properties' => array(
                    'id'
                )
            )
        )
    );

    protected $tableDataBundle = array(
        'properties' => null,
        'children' => array(
            'licenceVehicles' => array(
                'properties' => array(
                    'id',
                    'receivedDate',
                    'specifiedDate',
                    'deletedDate'
                ),
                'children' => array(
                    'goodsDiscs' => array(
                        'ceasedDate',
                        'discNo'
                    ),
                    'vehicle' => array(
                        'properties' => array(
                            'vrm',
                            'platedWeight'
                        )
                    )
                )
            )
        )
    );

    protected $actionTableDataBundle = array(
        'properties' => array(
            'id',
            'vrm',
            'licenceNo',
            'specifiedDate',
            'removalDate',
            'discNo'
        ),

    );

    /**
     * Test indexAction
     */
    public function testIndexAction()
    {
        $this->setUpAction('index');

        $response = $this->controller->indexAction();

        $table = $this->getTableFromView($response);

        $this->assertFalse($table->hasAction('reprint'));

        // Make sure we get a view not a response
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test indexAction
     */
    public function testIndexActionWithoutReprint()
    {
        $this->setUpAction('index');

        $response = array(
            'status' => array(
                'id' => ApplicationController::APPLICATION_STATUS_GRANTED
            )
        );

        $this->setRestResponse('Application', 'GET', $response, $this->applicationStatusBundle);

        $response = $this->controller->indexAction();

        $table = $this->getTableFromView($response);

        $this->assertTrue($table->hasAction('reprint'));

        // Make sure we get a view not a response
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test indexAction with crud action
     */
    public function testIndexActionWithCrudAction()
    {
        $this->setUpAction('index', null, array('action' => 'Add'));

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction with crud action
     */
    public function testIndexActionWithEditCrudAction()
    {
        $this->setUpAction('index', null, array('action' => 'Edit', 'id' => 1));

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction with crud action
     */
    public function testIndexActionWithAddCrudActionWithTooManyVehicles()
    {
        $this->setUpAction('index', null, array('action' => 'Add'));

        $bundle = array(
            'properties' => array(
                'totAuthVehicles'
            )
        );

        $response = array(
            'totAuthVehicles' => 1
        );

        $this->setRestResponse('Application', 'GET', $response, $bundle);

        $totalNumberOfVehiclesResponse = array(
            'licence' => array(
                'licenceVehicles' => array(
                    array(
                        'id' => 1
                    )
                )
            )
        );

        $totalNumberOfVehiclesBundle = array(
            'properties' => array(),
            'children' => array(
                'licence' => array(
                    'properties' => array(),
                    'children' => array(
                        'licenceVehicles' => array(
                            'properties' => array('id')
                        )
                    )
                )
            )
        );

        $this->setRestResponse('Application', 'GET', $totalNumberOfVehiclesResponse, $totalNumberOfVehiclesBundle);

        $response = $this->controller->indexAction();

        $flashMessenger = $this->controller->plugin('FlashMessenger');

        $this->assertEquals(1, count($flashMessenger->getCurrentMessagesFromNamespace('error')));

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction with crud action
     */
    public function testIndexActionWithAddCrudActionWithNotEnoughVehicles()
    {
        $this->setUpAction('index', null, array('action' => 'Add'));

        $bundle = array(
            'properties' => array(
                'totAuthVehicles'
            )
        );

        $response = array(
            'totAuthVehicles' => 2
        );

        $this->setRestResponse('Application', 'GET', $response, $bundle);

        $totalNumberOfVehiclesResponse = array(
            'licence' => array(
                'licenceVehicles' => array(
                    array(
                        'id' => 1
                    )
                )
            )
        );

        $totalNumberOfVehiclesBundle = array(
            'properties' => array(),
            'children' => array(
                'licence' => array(
                    'properties' => array(),
                    'children' => array(
                        'licenceVehicles' => array(
                            'properties' => array('id')
                        )
                    )
                )
            )
        );

        $this->setRestResponse('Application', 'GET', $totalNumberOfVehiclesResponse, $totalNumberOfVehiclesBundle);

        $response = $this->controller->indexAction();

        $flashMessenger = $this->controller->plugin('FlashMessenger');

        $this->assertEquals(0, count($flashMessenger->getCurrentMessagesFromNamespace('error')));

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction with submit
     */
    public function testIndexActionWithSubmit()
    {
        $this->setUpAction('index', null, array('foo' => 'bar'));

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test addAction
     */
    public function testAddAction()
    {
        $this->setUpAction('add');

        $response = $this->controller->addAction();

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test addAction with cancel
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
     * Test editAction with cancel
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
     * Test addAction with submit
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
                    'vrm' => 'AB12 CVB',
                    'platedWeight' => 100
                )
            )
        );

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->addAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test addAction with submit with addAnother
     */
    public function testAddActionWithSubmitWithAddAnother()
    {
        $this->setUpAction(
            'add',
            null,
            array(
                'data' => array(
                    'id' => '',
                    'version' => '',
                    'vrm' => 'AB12 CVB',
                    'platedWeight' => 100
                ),
                'form-actions' => array(
                    'addAnother' => 'Add another'
                )
            )
        );

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->addAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test addAction with submit with failure
     *
     * @expectedException \Exception
     */
    public function testAddActionWithSubmitWithFailuer()
    {
        $this->setUpAction(
            'add',
            null,
            array(
                'data' => array(
                    'id' => '',
                    'version' => '',
                    'vrm' => 'AB12 CVB',
                    'platedWeight' => 100
                )
            )
        );

        $this->setRestResponse('Vehicle', 'POST', '');

        $this->controller->setEnabledCsrf(false);
        $this->controller->addAction();
    }

    /**
     * Test editAction
     */
    public function testEditAction()
    {
        $this->setUpAction('edit', 1);

        $response = $this->controller->editAction();

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test editAction with submit
     */
    public function testEditActionWithSubmit()
    {
        $this->setUpAction(
            'edit',
            1, array(
                'data' => array(
                    'id' => 1,
                    'version' => 1,
                    'vrm' => 'AB12 CVB',
                    'platedWeight' => 100
                )
            )
        );

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->editAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test deleteAction
     */
    public function testDeleteAction()
    {
        $this->setUpAction('delete', 1);

        $response = $this->controller->deleteAction();

        $form = $this->getFormFromView($response);
        $this->assertEquals(
            'vehicle-remove-confirm-label',
            $form->get('data')->get('id')->getLabel('vehicle-remove-confirm-label')
        );

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test deleteAction
     */
    public function testDeleteActionWithSubmit()
    {
        $this->setUpAction('delete', 1, array('data' => array('id' => 1)));

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->deleteAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test deleteAction without id
     */
    public function testDeleteActionWithoutId()
    {
        $this->setUpAction('delete');

        $this->setRestResponse('LicenceVehicle', 'GET', array('Count' => 0, 'Results' => array()));

        $response = $this->controller->deleteAction();

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test addAction with submit
     */
    public function testAddActionWithSubmitWithVehicleOnAnotherLicence()
    {
        $this->setUpAction(
            'add',
            null,
            array(
                'data' => array(
                    'id' => '',
                    'version' => '',
                    'vrm' => 'AB12',
                    'platedWeight' => 100
                )
            )
        );

        $response = array(
            'Count' => 2,
            'Results' => array(
                array(
                    'licenceVehicles' => array(
                        array(
                            'licence' => array(
                                'id' => 20,
                                'licNo' => 'AB123'
                            )
                        )
                    )
                ),
                array(
                    'licenceVehicles' => array(
                        array(
                            'licence' => array(
                                'id' => 21,
                                'licNo' => '',
                                'applications' => array(
                                    array(
                                        'id' => 123
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $this->setRestResponse('Vehicle', 'GET', $response, $this->otherLicencesBundle);

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->addAction();

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test addAction with submit
     */
    public function testAddActionWithSubmitWithVehicleOnAnotherLicenceWithConfirm()
    {
        $this->setUpAction(
            'add',
            null,
            array(
                'data' => array(
                    'id' => '',
                    'version' => '',
                    'vrm' => 'AB12',
                    'platedWeight' => 100
                ),
                'licence-vehicle' => array(
                    'confirm-add' => 'y',
                    'receivedDate' => array('day' => '01', 'month' => '01', 'year' => '2014')
                )
            )
        );

        $response = array(
            'Count' => 2,
            'Results' => array(
                array(
                    'licenceVehicles' => array(
                        array(
                            'licence' => array(
                                'id' => 20,
                                'licNo' => 'AB123'
                            )
                        )
                    )
                ),
                array(
                    'licenceVehicles' => array(
                        array(
                            'licence' => array(
                                'id' => 21,
                                'licNo' => '',
                                'applications' => array(
                                    array(
                                        'id' => 123
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $this->setRestResponse('Vehicle', 'GET', $response, $this->otherLicencesBundle);

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->addAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction with crud action
     *
     * @group reprint
     */
    public function testIndexActionWithReprintCrudActionWithoutSelectingRow()
    {
        $this->setUpAction('index', null, array('action' => 'reprint'));

        $response = $this->controller->indexAction();

        $flashMessenger = $this->controller->plugin('FlashMessenger');

        $this->assertEquals(0, count($flashMessenger->getCurrentMessagesFromNamespace('error')));
        $this->assertEquals(1, count($flashMessenger->getCurrentMessagesFromNamespace('warning')));
    }

    /**
     * Test indexAction with crud action
     *
     * @group reprint
     */
    public function testIndexActionWithReprintCrudActionWithPendingDisc()
    {
        $this->setUpAction('index', null, array('action' => 'reprint', 'id' => array(1)));

        $discPendingBundle = array(
            'properties' => array(
                'id',
                'specifiedDate',
                'deletedDate'
            ),
            'children' => array(
                'goodsDiscs' => array(
                    'ceasedDate',
                    'discNo'
                )
            )
        );

        $response = array(
            'id' => 1,
            'specifiedDate' => '2014-01-01',
            'deletedDate' => null,
            'goodsDiscs' => array(
                array(
                    'ceasedDate' => null,
                    'discNo' => null
                )
            )
        );

        $this->setRestResponse('LicenceVehicle', 'GET', $response, $discPendingBundle);

        $response = $this->controller->indexAction();

        $flashMessenger = $this->controller->plugin('FlashMessenger');

        $this->assertEquals(1, count($flashMessenger->getCurrentMessagesFromNamespace('error')));

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction with crud action
     *
     * @group reprint
     */
    public function testIndexActionWithReprintCrudActionWithoutPendingDisc()
    {
        $this->setUpAction('index', null, array('action' => 'reprint', 'id' => array(1)));

        $discPendingBundle = array(
            'properties' => array(
                'id',
                'specifiedDate',
                'deletedDate'
            ),
            'children' => array(
                'goodsDiscs' => array(
                    'ceasedDate',
                    'discNo'
                )
            )
        );

        $response = array(
            'id' => 1,
            'specifiedDate' => '2014-01-01',
            'deletedDate' => null,
            'goodsDiscs' => array(
                array(
                    'ceasedDate' => '2014-01-01',
                    'discNo' => 1234
                )
            )
        );

        $this->setRestResponse('LicenceVehicle', 'GET', $response, $discPendingBundle);

        $response = $this->controller->indexAction();

        $flashMessenger = $this->controller->plugin('FlashMessenger');

        $this->assertEquals(0, count($flashMessenger->getCurrentMessagesFromNamespace('error')));

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test editAction
     *
     * @group reprint
     */
    public function testReprintAction()
    {
        $this->setUpAction('reprint', 1);

        $response = $this->controller->reprintAction();

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test editAction
     *
     * @group reprint
     */
    public function testReprintActionWithSubmit()
    {
        $this->setUpAction('reprint', 1, array('data' => array('id' => 1)));

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->reprintAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test editAction
     *
     * @group reprint
     */
    public function testReprintActionWithSubmitWithActiveDisc()
    {
        $this->setUpAction('reprint', 1, array('data' => array('id' => 1)));

        $response = array(
            'goodsDiscs' => array(
                array(
                    'id' => 1,
                    'version' => 1,
                    'ceasedDate' => null
                )
            )
        );

        $bundle = array(
            'properties' => array(),
            'children' => array(
                'goodsDiscs' => array(
                    'properties' => array(
                        'id',
                        'version',
                        'ceasedDate'
                    )
                )
            )
        );

        $this->setRestResponse('LicenceVehicle', 'GET', $response, $bundle);

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->reprintAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test editAction
     *
     * @group reprint
     */
    public function testReprintActionWithSubmitWithAlreadyCeasedDisc()
    {
        $this->setUpAction('reprint', 1, array('data' => array('id' => 1)));

        $response = array(
            'goodsDiscs' => array(
                array(
                    'id' => 1,
                    'version' => 1,
                    'ceasedDate' => '2014-01-01'
                )
            )
        );

        $bundle = array(
            'properties' => array(),
            'children' => array(
                'goodsDiscs' => array(
                    'properties' => array(
                        'id',
                        'version',
                        'ceasedDate'
                    )
                )
            )
        );

        $this->setRestResponse('LicenceVehicle', 'GET', $response, $bundle);

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->reprintAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test indexAction
     */
    public function testIndexActionWithMultipleVehicles()
    {
        $this->setUpAction('index');

        $response = array(
            'licenceVehicles' => array(
                array(
                    'id' => 1,
                    'receivedDate' => null,
                    'specifiedDate' => '2014-01-01',
                    'deletedDate' => null,
                    'goodsDisc' => array(
                        array(
                            'ceasedDate' => null,
                            'discNo' => 123
                        )
                    ),
                    'vehicle' => array(
                        'vrm' => 'AB12 ABG',
                        'platedWeight' => 100
                    )
                ),
                array(
                    'id' => 2,
                    'receivedDate' => null,
                    'specifiedDate' => '2014-01-01',
                    'deletedDate' => null,
                    'goodsDisc' => array(
                        array(
                            'ceasedDate' => '2014-01-01',
                            'discNo' => 1234
                        )
                    ),
                    'vehicle' => array(
                        'vrm' => 'DB12 ABG',
                        'platedWeight' => 150
                    )
                ),
                array(
                    'id' => 3,
                    'receivedDate' => null,
                    'specifiedDate' => '2014-01-01',
                    'deletedDate' => null,
                    'goodsDisc' => array(
                        array(
                            'ceasedDate' => '2014-01-01',
                            'discNo' => null
                        )
                    ),
                    'vehicle' => array(
                        'vrm' => 'DB12 ABG',
                        'platedWeight' => 150
                    )
                )
            )
        );

        $this->setRestResponse('Licence', 'GET', $response, $this->tableDataBundle);

        $response = $this->controller->indexAction();

        $table = $this->getTableFromView($response);

        $this->assertFalse($table->hasAction('reprint'));

        // Make sure we get a view not a response
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    public function testIndexActionWithDeleteCrudActionWithSingleId()
    {
        $this->setUpAction('index', null, array('action' => 'delete', 'id' => 1));

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    public function testIndexActionWithDeleteCrudActionWithSingleIdWithArray()
    {
        $this->setUpAction('index', null, array('action' => 'delete', 'id' => array(1)));

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    public function testIndexActionWithDeleteCrudAction()
    {
        $this->setUpAction('index', null, array('action' => 'delete', 'id' => array(1, 2, 3)));

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test deleteAction
     */
    public function testDeleteActionWithSubmitWithMultipleIds()
    {
        $this->setUpAction('delete', 1, array('data' => array('id' => '1,2,3')));

        $this->controller->setEnabledCsrf(false);
        $response = $this->controller->deleteAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

    /**
     * Test deleteAction
     */
    public function testDeleteActionWithIdsInQuery()
    {
        $this->setUpAction('delete', array(1, 2, 3));

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

            return $this->getLicenceData('goods');
        }

        if ($service == 'Application' && $method == 'GET'
            && $bundle == $this->applicationStatusBundle
        ) {

            return array(
                'status' => array(
                    'id' => ApplicationController::APPLICATION_STATUS_NOT_YET_SUBMITTED
                )
            );
        }

        if ($service == 'ApplicationCompletion' && $method == 'GET') {

            return $this->getApplicationCompletionData();
        }

        if ($service == 'VehicleHistoryView' && $method == 'GET' && $bundle == $this->actionTableDataBundle) {
            return array(
                array(
                    'id' => 1,
                    'vrm' => 'VRM1',
                    'licenceNo' => '123456',
                    'specifiedDate' => '2014-01-01 00:00:00',
                    'removalDate' => '2014-01-02 00:00:00',
                    'discNo' => 1234567
                )
            );
        }

        if ($service == 'Vehicle' && $method == 'POST') {
            return array('id' => 1);
        }

        if ($service == 'LicenceVehicle' && $method == 'POST') {
            return array('id' => 1);
        }

        if ($service == 'Vehicle' && $method == 'GET' && $bundle == $this->otherLicencesBundle) {
            return array(
                'Count' => 0,
                'Results' => array(
                )
            );
        }

        if ($service == 'Vehicle' && $method == 'GET') {
            return array(
                'id' => 1,
                'version' => 1,
                'vrm' => 'AB12 ABC',
                'isNovelty' => 'Y'
            );
        }

        if ($service == 'Licence' && $method == 'GET' && $bundle == $this->tableDataBundle) {
            return array(
                'licenceVehicles' => array(
                    array(
                        'id' => 1,
                        'receivedDate' => null,
                        'specifiedDate' => null,
                        'deletedDate' => null,
                        'goodsDisc' => array(
                            array(
                                'ceasedDate' => null,
                                'discNo' => 123
                            )
                        ),
                        'vehicle' => array(
                            'vrm' => 'AB12 ABG',
                            'platedWeight' => 100
                        )
                    ),
                    array(
                        'id' => 2,
                        'receivedDate' => null,
                        'specifiedDate' => null,
                        'deletedDate' => null,
                        'goodsDisc' => array(
                            array(
                                'ceasedDate' => null,
                                'discNo' => 1234
                            )
                        ),
                        'vehicle' => array(
                            'vrm' => 'DB12 ABG',
                            'platedWeight' => 150
                        )
                    )
                )
            );
        }

        $actionDataBundle = array(
            'properties' => array(
                'id',
                'version',
                'receivedDate',
                'deletedDate',
                'specifiedDate'
            ),
            'children' => array(
                'goodsDiscs' => array(
                    'properties' => array(
                        'discNo'
                    )
                ),
                'vehicle' => array(
                    'properties' => array(
                        'id',
                        'version',
                        'platedWeight',
                        'vrm'
                    )
                )
            )
        );

        if ($service == 'LicenceVehicle' && $method == 'GET' && $bundle == $actionDataBundle) {
            return array(
                'id' => 1,
                'version' => 1,
                'receivedDate' => null,
                'deletedDate' => null,
                'specifiedDate' => null,
                'goodsDiscs' => array(
                    array(
                        'discNo' => 123
                    )
                ),
                'vehicle' => array(
                    'id' => 1,
                    'version' => 1,
                    'platedWeight' => 100,
                    'vrm' => 'AB12 ABC'
                )
            );
        }

        $licenceVehicleBundle = array(
            'properties' => array(),
            'children' => array(
                'vehicle' => array(
                    'properties' => array('vrm')
                )
            )
        );

        if ($service == 'LicenceVehicle' && $method == 'GET' && $bundle == $licenceVehicleBundle) {
            return array(
                'Count' => 1,
                'Results' => array(
                    array(
                        'vehicle' => array(
                            'vrm' => 'RANDOM'
                        )
                    )
                )
            );
        }

    }
}
