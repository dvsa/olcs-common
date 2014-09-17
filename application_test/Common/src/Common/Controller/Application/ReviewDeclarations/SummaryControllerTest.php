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
                            'tradingNames' => array(
                            ),
                        )
                    ),
                    'contactDetails' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'emailAddress'
                        ),
                        'children' => array(
                            'phoneContacts' => array(
                                'properties' => array(
                                    'id',
                                    'version',
                                    'phoneNumber'
                                ),
                                'children' => array(
                                    'phoneContactType' => array(
                                        'properties' => array(
                                            'id'
                                        )
                                    )
                                )
                            ),
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
                            ),
                            'contactType' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    ),
                ),
            ),
            'documents' => array()
        )
    );

    protected $appDataResponse = array(
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
                    ),
                    'phoneContacts' => array(
                        array(
                            'id' => '1',
                            'phoneNumber' => '12345',
                            'phoneContactType' => array('id' => 'phone_t_tel')
                        )
                    ),
                    'emailAddress' => 'blah@blah.com'
                )
            )
        ),
        'documents' => array()
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
                                ),
                                'emailAddress' => 'blah@blah.com'
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
                            ),
                            'phoneContacts' => array(
                                array(
                                    'id' => '1',
                                    'phoneNumber' => '12345',
                                    'phoneContactType' => array('id' => 'phone_t_tel')
                                )
                            ),
                            'emailAddress' => 'blah@blah.com'
                        )
                    )
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
     * Mock the rest call.
     *
     * @todo Reconcile similar calls down when the entire review stage work has been completed.
     *
     * @param string $service
     * @param string $method
     * @param array $data
     * @param array $bundle
     */
    protected function mockRestCalls($service, $method, $data = array(), $bundle = array())
    {
        if ($service == 'Application'
                && $method == 'GET'
                && $bundle == ApplicationController::$applicationLicenceDataBundle) {

            return $this->getLicenceData();
        }

        if ($service == 'Application'
                && $method == 'GET'
                && $bundle == $this->appIdBundle) {

            return array(
                'id' => 1,
                'version' => 1,
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
                    'organisation' => array(
                        'type' => array(
                            'id' => ApplicationController::ORG_TYPE_REGISTERED_COMPANY
                        )
                    )
                )
            );
        }

        if ($service == 'Application'
                && $method == 'GET'
                && $bundle == $this->appDataBundle) {
            return $this->appDataResponse;
        }

        $organisationDataBundle = array(
            'properties' => array(),
            'children' => array(
                'licence' => array(
                    'children' => array(
                        'licenceType' => array(
                            'properties' => array(
                                'id'
                            )
                        ),
                        'organisation' => array(
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

        if ( $service == 'Application'
                && $method == 'GET'
                && $bundle == $organisationDataBundle ) {
            return array(
                'licence' => array(
                    'licenceType' => array(
                        'id' => 'ltyp_sn'
                    ),
                    'organisation' => array(
                        'type' => array(
                            'id' => ApplicationController::ORG_TYPE_REGISTERED_COMPANY
                        )
                    )
                )
            );
        }

        $organisationIdDataBundle = array(
            'children' => array(
                'licence' => array(
                    'children' => array(
                        'organisation' => array(
                            'properties' => array(
                                'id',
                                'version'
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

        if ( $service == 'Application'
                && $method == 'GET'
                && $bundle == $organisationIdDataBundle ) {
            return array(
                'id' => 1,
                'version' => 1,
                'licence' => array(
                    'organisation' => array(
                        'id' => 1,
                        'version' => 1,
                        'type' => array(
                            'id' => ApplicationController::ORG_TYPE_REGISTERED_COMPANY
                        )
                    )
                )
            );
        }

        $trafficAreaBundle = array(
            'children' => array(
                'licence' => array(
                    'children' => array(
                        'trafficArea' => array(
                            'properties' => array(
                                'name'
                            )
                        )
                    )
                )
            )
        );

        if ( $service == 'Application'
                && $method == 'GET'
                && $bundle == $trafficAreaBundle ) {
            return array(
                'licence' => array(
                    'organisation' => array(
                        'trafficArea' => array(
                            'name' => 'Deepest Darkest Peru'
                        )
                    )
                )
            );
        }

        $addressDataBundle = array(
            'properties' => array(),
            'children' => array(
                'licence' => array(
                    'properties' => array(),
                    'children' => array(
                        'organisation' => array(
                            'properties' => array(),
                            'children' => array(
                                'contactDetails' => array(
                                    'properties' => array(
                                        'id',
                                        'version',
                                        'emailAddress'
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
                                        ),
                                        'contactType' => array(
                                            'properties' => array(
                                                'id'
                                            )
                                        )
                                    )
                                ),
                            )
                        ),
                        'contactDetails' => array(
                            'properties' => array(
                                'id',
                                'version',
                                'emailAddress'
                            ),
                            'children' => array(
                                'phoneContacts' => array(
                                    'properties' => array(
                                        'id',
                                        'version',
                                        'phoneNumber'
                                    ),
                                    'children' => array(
                                        'phoneContactType' => array(
                                            'properties' => array(
                                                'id'
                                            )
                                        )
                                    )
                                ),
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
                                ),
                                'contactType' => array(
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

        if ( $service == 'Application'
                && $method == 'GET'
                && $bundle == $addressDataBundle ) {
            return $this->appDataResponse;
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

        $workshopBundle = array(
            'properties' => array(
                'id',
                'version'
            ),
            'children' => array(
                'licence' => array(
                    'children' => array(
                        'workshops' => array(
                            'properties' => array(
                                'id',
                                'isExternal'
                            ),
                            'children' => array(
                                'contactDetails' => array(
                                    'properties' => array(
                                        'fao'
                                    ),
                                    'children' => array(
                                        'address' => array(
                                            'properties' => array(
                                                'addressLine1',
                                                'addressLine2',
                                                'addressLine3',
                                                'addressLine4',
                                                'town',
                                                'postcode'
                                            ),
                                            'children' => array(
                                                'countryCode' => array(
                                                    'properties' => array('id')
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        if ( $service == 'Application'
                && $method == 'GET'
                && $bundle == $workshopBundle ) {
            return array(
                'id' => 1,
                'version' => 1,
                'licence' => array(
                    'workshops' => array(
                        array(
                            'id' => 1,
                            'isExternal' => 0,
                            'contactDetails' => array(
                                'fao' => 'Bob Smith',
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
                        'version' => 1,
                        'name' => 'name',
                        'companyNo' => '12345678'
                    )
                )
            );
        }

        if ( $service == 'ApplicationOperatingCentre' && $method == 'GET' ) {
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

        // Temporary debug to show when we haven't matched a REST call
        echo $service;
        var_dump($bundle);
    }
}
