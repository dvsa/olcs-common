<?php

/**
 * Test Abstract Business Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Entity\OrganisationEntityService;
use Common\BusinessService\Response;

/**
 * Test Abstract Business Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractBusinessDetailsControllerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock('\Common\Controller\Lva\AbstractBusinessDetailsController')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider indexActionGetProvider
     */
    public function testIndexActionGet($withTable, $extraExpectations)
    {
        // Stubbed Data
        $stubbedBusinessDetails = [
            'tradingNames' => [
                ['name' => 'foo'],
                ['name' => 'bar'],
            ],
            'version' => 1,
            'companyOrLlpNo' => '12345678',
            'name' => 'Foo ltd',
            'type' => [
                'id' => OrganisationEntityService::ORG_TYPE_LLP
            ],
            'contactDetails' => [
                'address' => [
                    'addressLine1' => 'Foo street'
                ]
            ]
        ];
        $stubbedNoB = [
            'sic1',
            'sic2'
        ];
        $expectedFormData = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar'
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockOrganisation = m::mock();
        $mockFormServiceManager = m::mock();
        $mockFormService = m::mock();
        $mockForm = m::mock();
        $mockScript = m::mock();

        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
        $this->sm->setService('Script', $mockScript);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(111)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('render')
            ->once()
            ->with('business_details', $mockForm)
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getBusinessDetailsData')
            ->once()
            ->with(111)
            ->andReturn($stubbedBusinessDetails)
            ->shouldReceive('getNatureOfBusinessesForSelect')
            ->once()
            ->with(111)
            ->andReturn($stubbedNoB);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $mockFormServiceManager->shouldReceive('get')
            ->once()
            ->with('lva--business_details')
            ->andReturn($mockFormService);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with(OrganisationEntityService::ORG_TYPE_LLP, 111)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('has')
            ->once()
            ->with('table')
            ->andReturn($withTable)
            ->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->andReturnSelf();

        // @NOTE rather than duplicate 90% of this test, we conditionally add expectations based on whether or
        // not the form has a table
        $extraExpectations($mockForm, $this->sm);

        $mockScript->shouldReceive('loadFile')
            ->once()
            ->with('lva-crud');

        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    /**
     * @dataProvider indexActionPostProvider
     */
    public function testIndexActionPostWithInvalidTradingNames($stubbedPost, $stubbedBusinessDetails)
    {
        $expectedFormData = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'submit_add_trading_name' => true,
                    'trading_name' => [
                        'foo',
                        'bar ',
                        '',
                        ''
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockOrganisation = m::mock();
        $mockFormServiceManager = m::mock();
        $mockFormService = m::mock();
        $mockForm = m::mock();
        $mockScript = m::mock();

        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
        $this->sm->setService('Script', $mockScript);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(111)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('render')
            ->once()
            ->with('business_details', $mockForm)
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getBusinessDetailsData')
            ->once()
            ->with(111)
            ->andReturn($stubbedBusinessDetails);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormServiceManager->shouldReceive('get')
            ->once()
            ->with('lva--business_details')
            ->andReturn($mockFormService);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with(OrganisationEntityService::ORG_TYPE_LLP, 111)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('has')
            ->once()
            ->with('table')
            ->andReturn(false)
            ->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->andReturnSelf()
            ->shouldReceive('setValidationGroup')
            ->once()
            ->with(['data' => ['tradingNames']])
            ->shouldReceive('isValid')
            ->once()
            ->andReturn(false);

        $mockScript->shouldReceive('loadFile')
            ->once()
            ->with('lva-crud');

        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    /**
     * @dataProvider indexActionPostProvider
     */
    public function testIndexActionPostWithValidTradingNames($stubbedPost, $stubbedBusinessDetails)
    {
        $expectedFormData = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'submit_add_trading_name' => true,
                    'trading_name' => [
                        'foo',
                        'bar ',
                        '',
                        ''
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockOrganisation = m::mock();
        $mockFormServiceManager = m::mock();
        $mockFormService = m::mock();
        $mockForm = m::mock();
        $mockScript = m::mock();

        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
        $this->sm->setService('Script', $mockScript);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(111)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('render')
            ->once()
            ->with('business_details', $mockForm)
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getBusinessDetailsData')
            ->with(111)
            ->andReturn($stubbedBusinessDetails);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormServiceManager->shouldReceive('get')
            ->once()
            ->with('lva--business_details')
            ->andReturn($mockFormService);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with(OrganisationEntityService::ORG_TYPE_LLP, 111)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('has')
            ->once()
            ->with('table')
            ->andReturn(false);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->andReturnSelf()
            ->shouldReceive('setValidationGroup')
            ->once()
            ->with(['data' => ['tradingNames']])
            ->shouldReceive('isValid')
            ->once()
            ->andReturn(true)
            ->shouldReceive('get->get->get->populateValues')
            ->once()
            ->with(['foo', 'bar', '']);

        $mockScript->shouldReceive('loadFile')
            ->once()
            ->with('lva-crud');

        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostWithCompanyLookup()
    {
        $stubbedPost = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'submit_lookup_company' => true,
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar',
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $stubbedBusinessDetails = [
            'type' => [
                'id' => OrganisationEntityService::ORG_TYPE_LLP
            ]
        ];
        $expectedFormData = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'submit_lookup_company' => true,
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar'
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockOrganisation = m::mock();
        $mockFormServiceManager = m::mock();
        $mockFormService = m::mock();
        $mockForm = m::mock();
        $mockScript = m::mock();
        $mockFormHelper = m::mock();

        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
        $this->sm->setService('Script', $mockScript);
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(111)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('render')
            ->once()
            ->with('business_details', $mockForm)
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getBusinessDetailsData')
            ->with(111)
            ->andReturn($stubbedBusinessDetails);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormServiceManager->shouldReceive('get')
            ->once()
            ->with('lva--business_details')
            ->andReturn($mockFormService);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with(OrganisationEntityService::ORG_TYPE_LLP, 111)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('has')
            ->once()
            ->with('table')
            ->andReturn(false)
            ->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->andReturnSelf();

        $mockFormHelper->shouldReceive('processCompanyNumberLookupForm')
            ->once()
            ->with($mockForm, $expectedFormData, 'data');

        $mockScript->shouldReceive('loadFile')
            ->once()
            ->with('lva-crud');

        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostWithInvalidForm()
    {
        $stubbedPost = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar',
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $stubbedBusinessDetails = [
            'type' => [
                'id' => OrganisationEntityService::ORG_TYPE_LLP
            ]
        ];
        $expectedFormData = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar'
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockOrganisation = m::mock();
        $mockFormServiceManager = m::mock();
        $mockFormService = m::mock();
        $mockForm = m::mock();
        $mockScript = m::mock();
        $mockFormHelper = m::mock();

        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
        $this->sm->setService('Script', $mockScript);
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(111)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('render')
            ->once()
            ->with('business_details', $mockForm)
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getBusinessDetailsData')
            ->with(111)
            ->andReturn($stubbedBusinessDetails);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormServiceManager->shouldReceive('get')
            ->once()
            ->with('lva--business_details')
            ->andReturn($mockFormService);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with(OrganisationEntityService::ORG_TYPE_LLP, 111)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('has')
            ->once()
            ->with('table')
            ->andReturn(false)
            ->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(false);

        $mockScript->shouldReceive('loadFile')
            ->once()
            ->with('lva-crud');

        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostWithValidFormWithFailedPersist()
    {
        $stubbedPost = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar',
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $stubbedBusinessDetails = [
            'type' => [
                'id' => OrganisationEntityService::ORG_TYPE_LLP
            ]
        ];
        $expectedFormData = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar'
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockOrganisation = m::mock();
        $mockFormServiceManager = m::mock();
        $mockFormService = m::mock();
        $mockForm = m::mock();
        $mockScript = m::mock();
        $mockFormHelper = m::mock();
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();
        $mockFlashMessenger = m::mock();

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService('Lva\BusinessDetails', $mockBusinessService);

        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
        $this->sm->setService('Script', $mockScript);
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $this->sm->setService('BusinessServiceManager', $bsm);
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(111)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('render')
            ->once()
            ->with('business_details', $mockForm)
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getBusinessDetailsData')
            ->with(111)
            ->andReturn($stubbedBusinessDetails);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormServiceManager->shouldReceive('get')
            ->once()
            ->with('lva--business_details')
            ->andReturn($mockFormService);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with(OrganisationEntityService::ORG_TYPE_LLP, 111)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('has')
            ->once()
            ->with('table')
            ->andReturn(false)
            ->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(true);

        $mockScript->shouldReceive('loadFile')
            ->once()
            ->with('lva-crud');

        $mockBusinessService->shouldReceive('process')
            ->with(
                [
                    'tradingNames' => [
                        'foo',
                        'bar'
                    ],
                    'orgId' => 111,
                    'data' => $expectedFormData,
                    'licenceId' => 222
                ]
            )
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(false)
            ->shouldReceive('getMessage')
            ->andReturn('MSG');

        $mockFlashMessenger->shouldReceive('addErrorMessage')
            ->with('MSG');

        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostWithValidFormWithSuccessPersist()
    {
        $stubbedPost = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar',
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $stubbedBusinessDetails = [
            'type' => [
                'id' => OrganisationEntityService::ORG_TYPE_LLP
            ]
        ];
        $expectedFormData = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar'
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockOrganisation = m::mock();
        $mockFormServiceManager = m::mock();
        $mockFormService = m::mock();
        $mockForm = m::mock();
        $mockFormHelper = m::mock();
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService('Lva\BusinessDetails', $mockBusinessService);

        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $this->sm->setService('BusinessServiceManager', $bsm);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(111)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('postSave')
            ->once()
            ->with('business_details')
            ->shouldReceive('completeSection')
            ->once()
            ->with('business_details')
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getBusinessDetailsData')
            ->with(111)
            ->andReturn($stubbedBusinessDetails);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormServiceManager->shouldReceive('get')
            ->once()
            ->with('lva--business_details')
            ->andReturn($mockFormService);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with(OrganisationEntityService::ORG_TYPE_LLP, 111)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('has')
            ->once()
            ->with('table')
            ->andReturn(false)
            ->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(true);

        $mockBusinessService->shouldReceive('process')
            ->with(
                [
                    'tradingNames' => [
                        'foo',
                        'bar'
                    ],
                    'orgId' => 111,
                    'data' => $expectedFormData,
                    'licenceId' => 222
                ]
            )
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostWithValidFormWithSuccessPersistWithTableWithoutCrudAction()
    {
        $stubbedPost = [
            'version' => 1,
            'table' => 'TABLE',
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar',
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $stubbedBusinessDetails = [
            'type' => [
                'id' => OrganisationEntityService::ORG_TYPE_LLP
            ]
        ];
        $expectedFormData = [
            'version' => 1,
            'table' => 'TABLE',
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar'
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockOrganisation = m::mock();
        $mockFormServiceManager = m::mock();
        $mockFormService = m::mock();
        $mockForm = m::mock();
        $mockFormHelper = m::mock();
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService('Lva\BusinessDetails', $mockBusinessService);

        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $this->sm->setService('BusinessServiceManager', $bsm);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(111)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('postSave')
            ->once()
            ->with('business_details')
            ->shouldReceive('getCrudAction')
            ->with(['TABLE'])
            ->andReturn(null)
            ->shouldReceive('completeSection')
            ->once()
            ->with('business_details')
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getBusinessDetailsData')
            ->with(111)
            ->andReturn($stubbedBusinessDetails);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormServiceManager->shouldReceive('get')
            ->once()
            ->with('lva--business_details')
            ->andReturn($mockFormService);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with(OrganisationEntityService::ORG_TYPE_LLP, 111)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('has')
            ->once()
            ->with('table')
            ->andReturn(false)
            ->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(true);

        $mockBusinessService->shouldReceive('process')
            ->with(
                [
                    'tradingNames' => [
                        'foo',
                        'bar'
                    ],
                    'orgId' => 111,
                    'data' => $expectedFormData,
                    'licenceId' => 222
                ]
            )
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testIndexActionPostWithValidFormWithSuccessPersistWithCrudAction()
    {
        $stubbedPost = [
            'version' => 1,
            'table' => 'TABLE',
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar',
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];
        $stubbedBusinessDetails = [
            'type' => [
                'id' => OrganisationEntityService::ORG_TYPE_LLP
            ]
        ];
        $expectedFormData = [
            'version' => 1,
            'table' => 'TABLE',
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'foo',
                        'bar'
                    ]
                ],
                'name' => 'Foo ltd',
                'type' => OrganisationEntityService::ORG_TYPE_LLP,
                'natureOfBusinesses' => [
                    'sic1',
                    'sic2'
                ]
            ],
            'registeredAddress' => [
                'addressLine1' => 'Foo street'
            ]
        ];

        // Mocks
        $mockRequest = m::mock();
        $mockOrganisation = m::mock();
        $mockFormServiceManager = m::mock();
        $mockFormService = m::mock();
        $mockForm = m::mock();
        $mockFormHelper = m::mock();
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService('Lva\BusinessDetails', $mockBusinessService);

        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $this->sm->setService('BusinessServiceManager', $bsm);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(111)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('postSave')
            ->once()
            ->with('business_details')
            ->shouldReceive('getCrudAction')
            ->with(['TABLE'])
            ->andReturn('CRUDACTION')
            ->shouldReceive('handleCrudAction')
            ->once()
            ->with('CRUDACTION')
            ->andReturn('RESPONSE');

        $mockOrganisation->shouldReceive('getBusinessDetailsData')
            ->with(111)
            ->andReturn($stubbedBusinessDetails);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormServiceManager->shouldReceive('get')
            ->once()
            ->with('lva--business_details')
            ->andReturn($mockFormService);

        $mockFormService->shouldReceive('getForm')
            ->once()
            ->with(OrganisationEntityService::ORG_TYPE_LLP, 111)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('has')
            ->once()
            ->with('table')
            ->andReturn(false)
            ->shouldReceive('setData')
            ->once()
            ->with($expectedFormData)
            ->andReturnSelf()
            ->shouldReceive('isValid')
            ->andReturn(true);

        $mockBusinessService->shouldReceive('process')
            ->with(
                [
                    'tradingNames' => [
                        'foo',
                        'bar'
                    ],
                    'orgId' => 111,
                    'data' => $expectedFormData,
                    'licenceId' => 222
                ]
            )
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        $response = $this->sut->indexAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testAddActionGet()
    {
        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $mockForm = m::mock();

        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(null)
            ->shouldReceive('render')
            ->once()
            ->with('add_subsidiary_company', $mockForm)
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $mockFormHelper->shouldReceive('createFormWithRequest')
            ->once()
            ->with('Lva\BusinessDetailsSubsidiaryCompany', $mockRequest)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with([])
            ->andReturnSelf();

        $response = $this->sut->addAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testAddActionPostInvalidForm()
    {
        // Stubbed data
        $stubbedPost = ['foo' => 'bar'];

        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $mockForm = m::mock();

        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(null)
            ->shouldReceive('render')
            ->once()
            ->with('add_subsidiary_company', $mockForm)
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormHelper->shouldReceive('createFormWithRequest')
            ->once()
            ->with('Lva\BusinessDetailsSubsidiaryCompany', $mockRequest)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($stubbedPost)
            ->andReturnSelf();

        $mockForm->shouldReceive('isValid')
            ->once()
            ->andReturn(false);

        $response = $this->sut->addAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testAddActionPostFailedPersist()
    {
        // Stubbed data
        $stubbedPost = ['foo' => 'bar'];

        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $mockForm = m::mock();
        $mockFlashMessenger = m::mock();
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService('Lva\CompanySubsidiary', $mockBusinessService);

        $this->sm->setService('BusinessServiceManager', $bsm);
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(null)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('render')
            ->once()
            ->with('add_subsidiary_company', $mockForm)
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormHelper->shouldReceive('createFormWithRequest')
            ->once()
            ->with('Lva\BusinessDetailsSubsidiaryCompany', $mockRequest)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($stubbedPost)
            ->andReturnSelf();

        $mockForm->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $mockBusinessService->shouldReceive('process')
            ->once()
            ->with(
                [
                    'id' => null,
                    'licenceId' => 222,
                    'foo' => 'bar'
                ]
            )
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->once()
            ->andReturn(false)
            ->shouldReceive('getMessage')
            ->once()
            ->andReturn('MSG');

        $mockFlashMessenger->shouldReceive('addErrorMessage')
            ->once()
            ->with('MSG');

        $response = $this->sut->addAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testAddActionPostSuccessPersist()
    {
        // Stubbed data
        $stubbedPost = ['foo' => 'bar'];

        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $mockForm = m::mock();
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService('Lva\CompanySubsidiary', $mockBusinessService);

        $this->sm->setService('BusinessServiceManager', $bsm);
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(null)
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('handlePostSave')
            ->once()
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true)
            ->shouldReceive('getPost')
            ->andReturn($stubbedPost);

        $mockFormHelper->shouldReceive('createFormWithRequest')
            ->once()
            ->with('Lva\BusinessDetailsSubsidiaryCompany', $mockRequest)
            ->andReturn($mockForm);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with($stubbedPost)
            ->andReturnSelf();

        $mockForm->shouldReceive('isValid')
            ->once()
            ->andReturn(true);

        $mockBusinessService->shouldReceive('process')
            ->once()
            ->with(
                [
                    'id' => null,
                    'licenceId' => 222,
                    'foo' => 'bar'
                ]
            )
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->once()
            ->andReturn(true);

        $response = $this->sut->addAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testEditActionGet()
    {
        // Mocks
        $mockRequest = m::mock();
        $mockFormHelper = m::mock();
        $mockForm = m::mock();
        $mockCompanySubsidiary = m::mock();

        $this->sm->setService('Helper\Form', $mockFormHelper);
        $this->sm->setService('Entity\CompanySubsidiary', $mockCompanySubsidiary);

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn(123)
            ->shouldReceive('render')
            ->once()
            ->with('edit_subsidiary_company', $mockForm)
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $mockFormHelper->shouldReceive('createFormWithRequest')
            ->once()
            ->with('Lva\BusinessDetailsSubsidiaryCompany', $mockRequest)
            ->andReturn($mockForm);

        $mockCompanySubsidiary->shouldReceive('getById')
            ->with(123)
            ->andReturn(['foo' => 'bar']);

        $mockForm->shouldReceive('setData')
            ->once()
            ->with(['data' => ['foo' => 'bar']])
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                ->shouldReceive('remove')
                ->with('addAnother')
                ->getMock()
            );

        $response = $this->sut->editAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testDeleteActionWithFailedPersist()
    {
        // Mocks
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();
        $mockFlashMessenger = m::mock();
        $mockRequest = m::mock();

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService('Lva\DeleteCompanySubsidiary', $mockBusinessService);

        $this->sm->setService('BusinessServiceManager', $bsm);
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn('123,321')
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('postSave')
            ->with('business_details')
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('foo')
            ->shouldReceive('getIdentifier')
            ->andReturn(333);

        $this->sut->shouldReceive('redirect->toRouteAjax')
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true);

        $mockBusinessService->shouldReceive('process')
            ->once()
            ->with(
                [
                    'ids' => [123, 321],
                    'licenceId' => 222
                ]
            )
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(false)
            ->shouldReceive('getMessage')
            ->andReturn('MSG');

        $mockFlashMessenger->shouldReceive('addErrorMessage')
            ->with('MSG');

        $response = $this->sut->deleteAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function testDeleteActionWithSuccessPersist()
    {
        // Mocks
        $mockBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockResponse = m::mock();
        $mockRequest = m::mock();

        $bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $bsm->setService('Lva\DeleteCompanySubsidiary', $mockBusinessService);

        $this->sm->setService('BusinessServiceManager', $bsm);

        // Expectations
        $this->sut
            ->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('child_id')
            ->andReturn('123,321')
            ->shouldReceive('getLicenceId')
            ->andReturn(222)
            ->shouldReceive('postSave')
            ->with('business_details')
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('foo')
            ->shouldReceive('getIdentifier')
            ->andReturn(333);

        $this->sut->shouldReceive('redirect->toRouteAjax')
            ->andReturn('RESPONSE');

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true);

        $mockBusinessService->shouldReceive('process')
            ->once()
            ->with(
                [
                    'ids' => [123, 321],
                    'licenceId' => 222
                ]
            )
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        $response = $this->sut->deleteAction();

        $this->assertEquals('RESPONSE', $response);
    }

    public function indexActionPostProvider()
    {
        return [
            [
                [
                    'version' => 1,
                    'data' => [
                        'companyNumber' => [
                            'company_number' => '12345678'
                        ],
                        'tradingNames' => [
                            'submit_add_trading_name' => true,
                            'trading_name' => [
                                'foo',
                                'bar ',
                                '',
                                ''
                            ]
                        ],
                        'name' => 'Foo ltd',
                        'type' => OrganisationEntityService::ORG_TYPE_LLP,
                        'natureOfBusinesses' => [
                            'sic1',
                            'sic2'
                        ]
                    ],
                    'registeredAddress' => [
                        'addressLine1' => 'Foo street'
                    ]
                ],
                [
                    'type' => [
                        'id' => OrganisationEntityService::ORG_TYPE_LLP
                    ]
                ]
            ],
            [
                [
                    'version' => 1,
                    'data' => [
                        'tradingNames' => [
                            'submit_add_trading_name' => true,
                            'trading_name' => [
                                'foo',
                                'bar ',
                                '',
                                ''
                            ]
                        ],
                        'name' => 'Foo ltd',
                        'type' => OrganisationEntityService::ORG_TYPE_LLP,
                        'natureOfBusinesses' => [
                            'sic1',
                            'sic2'
                        ]
                    ],
                    'registeredAddress' => [
                        'addressLine1' => 'Foo street'
                    ]
                ],
                [
                    'type' => [
                        'id' => OrganisationEntityService::ORG_TYPE_LLP
                    ],
                    'companyOrLlpNo' => '12345678',
                ]
            ],
            [
                [
                    'version' => 1,
                    'data' => [
                        'companyNumber' => [],
                        'tradingNames' => [
                            'submit_add_trading_name' => true,
                            'trading_name' => [
                                'foo',
                                'bar ',
                                '',
                                ''
                            ]
                        ],
                        'name' => 'Foo ltd',
                        'type' => OrganisationEntityService::ORG_TYPE_LLP,
                        'natureOfBusinesses' => [
                            'sic1',
                            'sic2'
                        ]
                    ],
                    'registeredAddress' => [
                        'addressLine1' => 'Foo street'
                    ]
                ],
                [
                    'type' => [
                        'id' => OrganisationEntityService::ORG_TYPE_LLP
                    ],
                    'companyOrLlpNo' => '12345678',
                ]
            ],
            [
                [
                    'version' => 1,
                    'data' => [
                        'companyNumber' => [
                            'company_number' => '12345678'
                        ],
                        'tradingNames' => [
                            'submit_add_trading_name' => true,
                            'trading_name' => [
                                'foo',
                                'bar ',
                                '',
                                ''
                            ]
                        ],
                        'type' => OrganisationEntityService::ORG_TYPE_LLP,
                        'natureOfBusinesses' => [
                            'sic1',
                            'sic2'
                        ]
                    ],
                    'registeredAddress' => [
                        'addressLine1' => 'Foo street'
                    ]
                ],
                [
                    'type' => [
                        'id' => OrganisationEntityService::ORG_TYPE_LLP
                    ],
                    'name' => 'Foo ltd',
                ]
            ],
        ];
    }

    public function indexActionGetProvider()
    {
        return [
            'withTable' => [
                true,
                function ($mockForm, $sm) {
                    $stubbedSubsidiaries = [
                        'foo',
                        'cake'
                    ];

                    $mockCompanySubsidiary = m::mock();
                    $mockTableBuilder = m::mock();
                    $mockTable = m::mock();
                    $mockFormHelper = m::mock();
                    $mockTableFieldset = m::mock();

                    $sm->setService('Entity\CompanySubsidiary', $mockCompanySubsidiary);
                    $sm->setService('Table', $mockTableBuilder);
                    $sm->setService('Helper\Form', $mockFormHelper);

                    $mockCompanySubsidiary->shouldReceive('getForLicence')
                        ->once()
                        ->with(222)
                        ->andReturn($stubbedSubsidiaries);

                    $mockTableBuilder->shouldReceive('prepareTable')
                        ->once()
                        ->with('lva-subsidiaries', $stubbedSubsidiaries)
                        ->andReturn($mockTable);

                    $mockForm->shouldReceive('get')
                        ->once()
                        ->with('table')
                        ->andReturn($mockTableFieldset);

                    $mockFormHelper->shouldReceive('populateFormTable')
                        ->once()
                        ->with($mockTableFieldset, $mockTable);
                }
            ],
            'withoutTable' => [
                false,
                function ($mockForm, $sm) {
                }
            ]
        ];
    }
}
