<?php

/**
 * Summary Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace CommonTest\Controller\Application\ReviewDeclarations;

use CommonTest\Controller\Traits\TestBackButtonTrait;
use CommonTest\Controller\Application\AbstractApplicationControllerTestCase;
use Common\Controller\Application\Application\ApplicationController;

/**
 * Summary Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class SummaryControllerTest extends AbstractApplicationControllerTestCase
{
    use TestBackButtonTrait;

    protected $controllerName =  '\Common\Controller\Application\ReviewDeclarations\SummaryController';

    protected $defaultRestResponse = array();

    protected $appDataBundle = array(
        'properties' => 'ALL',
        'children' => array(
            'licence' => array(
                'children' => array(
                    'goodsOrPsv' => array(
                        'properties' => array('id')
                    ),
                    'licenceType' => array(
                        'properties' => array('id')
                    ),
                    'tachographIns' => array(
                        'properties' => array('id')
                    )
                )
            ),
            'documents' => array()
        )
    );

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
     * Test indexAction with form alterations
     */
    public function testIndexActionWithFormAlterations()
    {
        $this->setUpAction('index');

        $this->setRestResponse(
            'Application',
            'GET',
            array(
                'id' => 1,
                'prevConviction' => true,
                'isMaintenanceSuitable' => 'Y',
                'safetyConfirmation' => 'Y',
                'psvOperateSmallVhl' => 'Y',
                'psvSmallVhlNotes' => '',
                'psvSmallVhlConfirmation' => 'Y',
                'psvNoSmallVhlConfirmation' => 'Y',
                'psvLimousines' => 'Y',
                'psvNoLimousineConfirmation' => 0,
                'psvOnlyLimousinesConfirmation' => 0,
                'licence' => array(
                    'id' => 10,
                    'version' => 1,
                    'goodsOrPsv' => array(
                        'id' => ApplicationController::LICENCE_CATEGORY_GOODS_VEHICLE
                    ),
                    'niFlag' => 0,
                    'licenceType' => array(
                        'id' => ApplicationController::LICENCE_TYPE_STANDARD_NATIONAL
                    ),
                    'organisation' => array(
                        'type' => array(
                            'id' => ApplicationController::ORG_TYPE_REGISTERED_COMPANY
                        )
                    ),
                    'safetyInsVehicles' => 2,
                    'safetyInsTrailers' => 2,
                    'safetyInsVaries' => 'N',
                    'tachographInsName' => 'Bob',
                    'tachographIns' => array(
                        'id' => 'tach_internal'
                    ),
                    'workshops' => array()
                ),
                'documents' => array()
            ),
            $this->appDataBundle
        );

        $response = $this->controller->indexAction();

        // Make sure we get a view not a response
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test simpleAction
     */
    public function testSimpleAction()
    {
        $this->setUpAction('simple');

        $response = $this->controller->simpleAction();

        // Make sure we get a view not a response
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $response);
    }

    /**
     * Test indexAction with submit
     */
    public function testIndexActionWithSubmit()
    {
        $this->setUpAction('index', null, array('foo' => 'bar'));

        $this->controller->setEnabledCsrf(false);
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

            return $this->getLicenceData();
        }

        if ($service == 'Application' && $method == 'GET' && $bundle == $this->appDataBundle) {
            return array(
                'id' => 1,
                'prevConviction' => true,
                'isMaintenanceSuitable' => 'Y',
                'safetyConfirmation' => 'Y',
                'psvOperateSmallVhl' => 'Y',
                'psvSmallVhlNotes' => '',
                'psvSmallVhlConfirmation' => 'Y',
                'psvNoSmallVhlConfirmation' => 'Y',
                'psvLimousines' => 'Y',
                'psvNoLimousineConfirmation' => 0,
                'psvOnlyLimousinesConfirmation' => 0,
                'licence' => array(
                    'id' => 10,
                    'version' => 1,
                    'goodsOrPsv' => array(
                        'id' => ApplicationController::LICENCE_CATEGORY_GOODS_VEHICLE
                    ),
                    'niFlag' => 0,
                    'licenceType' => array(
                        'id' => ApplicationController::LICENCE_TYPE_STANDARD_NATIONAL
                    ),
                    'organisation' => array(
                        'type' => array(
                            'id' => ApplicationController::ORG_TYPE_REGISTERED_COMPANY
                        )
                    ),
                    'safetyInsVehicles' => 2,
                    'safetyInsTrailers' => 2,
                    'safetyInsVaries' => 'N',
                    'tachographInsName' => 'Bob',
                    'tachographIns' => array(
                        'id' => 'tach_internal'
                    ),
                    'workshops' => array()
                ),
                'documents' => array()
            );
        }

        if ($service == 'ApplicationCompletion' && $method == 'GET') {

            return $this->getApplicationCompletionData();
        }

        $convictionDataBundle = array(
            'properties' => array(
                'id',
                'convictionDate',
                'convictionCategory',
                'notes',
                'courtFpn',
                'categoryText',
                'penalty',
                'title',
                'forename',
                'familyName'
            )
        );

        if ($service == 'PreviousConviction' && $method === 'GET' && $bundle == $convictionDataBundle) {
            return array(
                'Count'  => 1,
                'Results' => array(
                    array(
                        'id' => 1,
                        'convictionDate' => '01/01/2014',
                        'convictionCategory' => 'Offence',
                        'notes' => 'No MOT',
                        'courtFpn' => 'Leeds court',
                        'penalty' => '100£',
                        'title' => 'Mr',
                        'forename' => 'Alex',
                        'familyName' => 'P'
                    )
                )
            );
        }

        if ($service == 'ApplicationOperatingCentre' && $method == 'GET') {
            return array(
                'Count' => 1,
                'Results' => array(
                    array(
                        'id' => 1,
                        'adPlaced' => 1,
                        'permission' => 1,
                        'noOfVehiclesPossessed' => 10,
                        'noOfTrailersPossessed' => 10,
                        'operatingCentre' => array(
                            'address' => array(
                                'id' => 1,
                                'addressLine1' => '123 Street',
                                'addressLine2' => 'Address 2',
                                'addressLine3' => 'Address 3',
                                'addressLine4' => 'Address 4',
                                'town' => 'City',
                                'countryCode' => array(
                                    'id' => 'GB'
                                ),
                                'postcode' => 'AB1 1AB'
                            )
                        )
                    )
                )
            );
        }
    }
}
