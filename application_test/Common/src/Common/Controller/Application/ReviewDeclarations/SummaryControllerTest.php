<?php

/**
 * Summary Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace CommonTest\Controller\Application\ReviewDeclarations;

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
                    ),
                    'organisation' => array(
                        'children' => array(
                            'type' => array(
                            ),
                            'contactDetails' => array(
                                'children' => array(
                                    'contactType' => array(
                                        'properties' => array('id')
                                    ),
                                    'address' => array(
                                        'properties' => array(
                                            'id',
                                            'addressLine1',
                                            'addressLine2',
                                            'addressLine3',
                                            'addressLine4',
                                            'town',
                                            'postcode',
                                        ),
                                        'children' => array(
                                            'countryCode' => array(
                                                'properties' => array('id')
                                            )
                                        )
                                    ),
                                    'phoneContacts' => array(
                                    ),
                                ),
                            ),
                        )
                    )
                )
            ),
            'documents' => array()
        )
    );

    protected $appIdBundle = array(
        'properties' => array(
            'id',
            'version',
            'licence' => array(
                'children' => array(
                    'organisation' => array(
                        'children' => array(
                            'type' => array(
                                'properties' => array(
                                    'id'
                                )
                            ),
                        )
                    )
                )
            )
        )
    );

    /**
     * Test back button
     */
    public function testBackButton()
    {
        $this->setUpAction('index', null, array('form-actions' => array('back' => 'Back')));

        $response = $this->controller->indexAction();

        $this->assertInstanceOf('Zend\Http\Response', $response);
    }

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
                        ),
                        'companyOrLlpNo' => 12345678,
                        'name' => 'Bob Ltd',
                        'contactDetails' => array(
                            array(
                                'contactType' => array(
                                    'id' => 'ct_oc'
                                ),
                                'address' => array(
                                    'addressLine1' => 'Shapely Industrial Estate',
                                    'addressLine2' => 'Unit 9',
                                    'addressLine3' => 'Harehills',
                                    'addressLine4' => '',
                                    'town' => 'Leeds',
                                    'postcode' => 'LS9 2FA',
                                    'id' => 21,
                                    'countryCode' => array(
                                        'id' => 'GB'
                                    )
                                )
                            )
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

        if ($service == 'Application' && $method == 'GET' && $bundle == $this->appIdBundle) {
            return array(
                'id' => 1,
                'version' => 1,
                'licence' => array(
                    'organisation' => array(
                        'id' => 1
                    )
                )
            );
        }

        if ($service == 'Application' && $method == 'GET' && $bundle == $this->appDataBundle) {
            return array(
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
                        ),
                        'companyOrLlpNo' => 12345678,
                        'name' => 'Bob Ltd',
                        'contactDetails' => array(
                            array(
                                'contactType' => array(
                                    'id' => 'ct_oc'
                                ),
                                'address' => array(
                                    'addressLine1' => 'Shapely Industrial Estate',
                                    'addressLine2' => 'Unit 9',
                                    'addressLine3' => 'Harehills',
                                    'addressLine4' => '',
                                    'town' => 'Leeds',
                                    'postcode' => 'LS9 2FA',
                                    'id' => 21,
                                    'countryCode' => array(
                                        'id' => 'GB'
                                    )
                                )
                            )
                        )
                    ),
                    'safetyInsVehicles' => 2,
                    'safetyInsTrailers' => 2,
                    'safetyInsVaries' => 'N',
                    'tachographInsName' => 'Bob',
                    'tachographIns' => array(
                        'id' => 'tach_internal'
                    ),
                    'workshops' => array(),
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

        $companySubsidiaryBundle = array(
            'properties' => array(
                'id',
                'version',
                'name',
                'companyNo'
            )
        );

        if ( $service == 'CompanySubsidiary' && $method === 'GET' && $bundle == $companySubsidiaryBundle ) {
            return array(
                'Count' => 1,
                'Results' => array(
                    array(
                        'id' => 1,
                        'version' => 1,
                        'name' => 'name',
                        'companyNo' => '12345678'
                    )
                )
            );
        }

        $peopleBundle = array(
            'properties' => array('position'),
            'children' => array(
                'person' => array(
                    'properties' => array(
                        'id',
                        'title',
                        'forename',
                        'familyName',
                        'birthDate',
                        'otherName'
                    )
                )
            )
        );

        if ( $service == 'OrganisationPerson' && $method === 'GET' && $bundle == $peopleBundle ) {
            return array(
                'Count' => 2,
                'Results' => array (
                    array(
                        'person' => array(
                            'forename' => 'Keith',
                            'familyName' => 'Chegwin',
                            'otherName' => 'Albert',
                            'id' => 78,
                            'birthDate' => '1975-01-17',
                            'title' => 'Mr'
                        )
                    )
                )
            );
        }
    }
}
