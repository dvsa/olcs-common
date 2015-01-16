<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

/**
 * Test Abstract Business Details Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractBusinessDetailsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractBusinessDetailsController');
    }

    public function testGetIndexActionForSoleTraderOrganisation()
    {
        $form = $this->createMockForm('Lva\BusinessDetails');

        $this->mockRender();

        $this->mockOrgData('org_t_st');

        $form->shouldReceive('setData')
            ->with(
                [
                    'version' => 1,
                    'data' => [
                        'companyNumber' => [
                            'company_number' => '12345678'
                        ],
                        'tradingNames' => [
                            'trading_name' => ['tn 1']
                        ],
                        'name' => 'An Org',
                        'type' => 'org_t_st',
                        'natureOfBusiness' => [1]
                    ],
                    'registeredAddress' => ['foo' => 'bar']
                ]
            );

        $this->shouldRemoveElements(
            $form,
            [
                'table',
                'data->companyNumber',
                'registeredAddress',
                'data->name'
            ]
        );

        $element = m::mock()->shouldReceive('setOptions')
            ->shouldReceive('getOptions')
            ->andReturn([])
            ->getMock();

        $typeElement = m::mock()->shouldReceive('setValue')
            ->with('org_t_st')
            ->getMock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('editBusinessType')
            ->andReturn($element)
            ->shouldReceive('get')
            ->with('type')
            ->andReturn($typeElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $form->shouldReceive('has')
            ->with('table')
            ->andReturn(false);

        $this->sut->indexAction();

        $this->assertEquals('business_details', $this->view);
    }

    public function testGetIndexActionForLimitedCompanyOrganisation()
    {
        $form = $this->createMockForm('Lva\BusinessDetails');

        $this->mockRender();

        $this->mockOrgData('org_t_rc');

        $form->shouldReceive('setData')
            ->with(
                [
                    'version' => 1,
                    'data' => [
                        'companyNumber' => [
                            'company_number' => '12345678'
                        ],
                        'tradingNames' => [
                            'trading_name' => ['tn 1']
                        ],
                        'name' => 'An Org',
                        'type' => 'org_t_rc',
                        'natureOfBusiness' => [1]
                    ],
                    'registeredAddress' => ['foo' => 'bar']
                ]
            );

        $element = m::mock()->shouldReceive('setOptions')
            ->shouldReceive('getOptions')
            ->andReturn([])
            ->getMock();

        $typeElement = m::mock()->shouldReceive('setValue')
            ->with('org_t_rc')
            ->getMock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('editBusinessType')
            ->andReturn($element)
            ->shouldReceive('get')
            ->with('type')
            ->andReturn($typeElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $form->shouldReceive('has')
            ->with('table')
            ->andReturn(true);

        $this->mockEntity('CompanySubsidiary', 'getAllForOrganisation')
            ->with(12)
            ->andReturn([]);

        $mockTable = m::mock();

        $this->mockService('Table', 'prepareTable')
            ->with('lva-subsidiaries', [])
            ->andReturn($mockTable);

        $tableElement = m::mock()
            ->shouldReceive('setTable')
            ->with($mockTable)
            ->getMock();

        $tableFieldset = m::mock()
            ->shouldReceive('get')
            ->with('table')
            ->andReturn($tableElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('table')
            ->andReturn($tableFieldset);

        $this->sut->indexAction();

        $this->assertEquals('business_details', $this->view);
    }

    public function testGetIndexActionForPartnershipOrganisation()
    {
        $form = $this->createMockForm('Lva\BusinessDetails');

        $this->mockRender();

        $this->mockOrgData('org_t_p');

        $form->shouldReceive('setData')
            ->with(
                [
                    'version' => 1,
                    'data' => [
                        'companyNumber' => [
                            'company_number' => '12345678'
                        ],
                        'tradingNames' => [
                            'trading_name' => ['tn 1']
                        ],
                        'name' => 'An Org',
                        'type' => 'org_t_p',
                        'natureOfBusiness' => [1]
                    ],
                    'registeredAddress' => ['foo' => 'bar']
                ]
            );

        $element = m::mock()->shouldReceive('setOptions')
            ->shouldReceive('getOptions')
            ->andReturn([])
            ->getMock();

        $typeElement = m::mock()->shouldReceive('setValue')
            ->with('org_t_p')
            ->getMock();

        $nameFieldset = m::mock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('editBusinessType')
            ->andReturn($element)
            ->shouldReceive('get')
            ->with('type')
            ->andReturn($typeElement)
            ->shouldReceive('get')
            ->with('name')
            ->andReturn($nameFieldset)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $formHelper = $this->getMockFormHelper();
        $formHelper->shouldReceive('alterElementLabel')
            ->with($nameFieldset, '.partnership', 1);

        $form->shouldReceive('has')
            ->with('table')
            ->andReturn(false);

        $this->shouldRemoveElements(
            $form,
            [
                'table',
                'data->companyNumber',
                'registeredAddress',
                'data->name',
                'data->tradingNames'
            ]
        );

        $this->sut->indexAction();

        $this->assertEquals('business_details', $this->view);
    }

    public function testGetIndexActionForOtherOrganisation()
    {
        $form = $this->createMockForm('Lva\BusinessDetails');

        $this->mockRender();

        $this->mockOrgData('org_t_pa');

        $form->shouldReceive('setData')
            ->with(
                [
                    'version' => 1,
                    'data' => [
                        'companyNumber' => [
                            'company_number' => '12345678'
                        ],
                        'tradingNames' => [
                            'trading_name' => ['tn 1']
                        ],
                        'name' => 'An Org',
                        'type' => 'org_t_pa',
                        'natureOfBusiness' => [1]
                    ],
                    'registeredAddress' => ['foo' => 'bar']
                ]
            );

        $element = m::mock()->shouldReceive('setOptions')
            ->shouldReceive('getOptions')
            ->andReturn([])
            ->getMock();

        $typeElement = m::mock()->shouldReceive('setValue')
            ->with('org_t_pa')
            ->getMock();

        $nameFieldset = m::mock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('editBusinessType')
            ->andReturn($element)
            ->shouldReceive('get')
            ->with('type')
            ->andReturn($typeElement)
            ->shouldReceive('get')
            ->with('name')
            ->andReturn($nameFieldset)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $formHelper = $this->getMockFormHelper();
        $formHelper->shouldReceive('alterElementLabel')
            ->with($nameFieldset, '.other', 1);

        $form->shouldReceive('has')
            ->with('table')
            ->andReturn(false);

        $this->shouldRemoveElements(
            $form,
            [
                'table',
                'data->companyNumber',
                'registeredAddress',
                'data->name',
                'data->tradingNames'
            ]
        );

        $this->sut->indexAction();

        $this->assertEquals('business_details', $this->view);
    }

    public function testPostWithCompanyNumberLookup()
    {
        $form = $this->createMockForm('Lva\BusinessDetails');

        $this->mockOrgData('org_t_rc');

        $postData = [
            'data' => [
                'companyNumber' => [
                    'submit_lookup_company' => true
                ]
            ]
        ];
        $this->setPost($postData);

        $element = m::mock()->shouldReceive('setOptions')
            ->shouldReceive('getOptions')
            ->andReturn([])
            ->getMock();

        $typeElement = m::mock()->shouldReceive('setValue')
            ->with('org_t_rc')
            ->getMock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('editBusinessType')
            ->andReturn($element)
            ->shouldReceive('get')
            ->with('type')
            ->andReturn($typeElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $form->shouldReceive('has')
            ->with('table')
            ->andReturn(false);

        $form->shouldReceive('setData');

        $formHelper = $this->getMockFormHelper();
        $formHelper->shouldReceive('processCompanyNumberLookupForm')
            ->with($form, $postData, 'data');

        $this->mockRender();

        $this->sut->indexAction();
    }

    public function testPostWithTradingNames()
    {
        $form = $this->createMockForm('Lva\BusinessDetails');

        $this->mockOrgData('org_t_rc');

        $postData = [
            'data' => [
                'tradingNames' => [
                    'submit_add_trading_name' => true,
                    'trading_name' => [
                        '  ', 'tn 1', 'tn 2'
                    ]
                ]
            ]
        ];
        $this->setPost($postData);

        $element = m::mock()->shouldReceive('setOptions')
            ->shouldReceive('getOptions')
            ->andReturn([])
            ->getMock();

        $typeElement = m::mock()->shouldReceive('setValue')
            ->with('org_t_rc')
            ->getMock();

        $tnElem = m::mock()->shouldReceive('populateValues')
            ->with(['tn 1', 'tn 2', ''])
            ->getMock();

        $tnFieldset = m::mock()->shouldReceive('get')
            ->with('trading_name')
            ->andReturn($tnElem)
            ->getMock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('editBusinessType')
            ->andReturn($element)
            ->shouldReceive('get')
            ->with('type')
            ->andReturn($typeElement)
            ->shouldReceive('get')
            ->with('tradingNames')
            ->andReturn($tnFieldset)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $form->shouldReceive('has')
            ->with('table')
            ->andReturn(false);

        $form->shouldReceive('setData')
            ->shouldReceive('isValid')
            ->andReturn(true)
            ->shouldReceive('setValidationGroup')
            ->with(['data' => ['tradingNames']]);

        $this->mockRender();

        $this->sut->indexAction();
    }

    public function testPostWithValidData()
    {
        $form = $this->createMockForm('Lva\BusinessDetails');

        $this->mockOrgData('org_t_rc');

        $postData = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'name' => 'Company Name',
                'tradingNames' => [
                    'trading_name' => [
                        'tn 1'
                    ]
                ],
                'natureOfBusiness' => [1,2]
            ],
            'registeredAddress' => ['foo' => 'bar']
        ];
        $this->setPost($postData);

        $element = m::mock()->shouldReceive('setOptions')
            ->shouldReceive('getOptions')
            ->andReturn([])
            ->getMock();

        $typeElement = m::mock()->shouldReceive('setValue')
            ->with('org_t_rc')
            ->getMock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('editBusinessType')
            ->andReturn($element)
            ->shouldReceive('get')
            ->with('type')
            ->andReturn($typeElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $form->shouldReceive('has')
            ->with('table')
            ->andReturn(false);

        $form->shouldReceive('setData')
            ->shouldReceive('isValid')
            ->andReturn(true);

        $this->sut
            ->shouldReceive('getLicenceId')
            ->andReturn(7);

        $this->mockEntity('TradingNames', 'save')
            ->with(
                [
                    'organisation' => 12,
                    'licence' => 7,
                    'tradingNames' => [
                        ['name' => 'tn 1']
                    ]
                ]
            );

        $this->mockEntity('Address', 'save')
            ->with(['foo' => 'bar'])
            ->andReturn(['id' => 4321]);

        $this->mockEntity('ContactDetails', 'save')
            ->with(
                [
                    'address' => 4321,
                    'contactType' => 'ct_reg'
                ]
            )
            ->andReturn(['id' => 3]);

        $this->mockEntity('Organisation', 'save')
            ->with(
                [
                    'version' => 1,
                    'companyOrLlpNo' => '12345678',
                    'name' => 'Company Name',
                    'id' => 12,
                    'contactDetails' => 3
                ]
            );

        $this->mockEntity('OrganisationNatureOfBusiness', 'save')
            ->with(['foo' => 'bar'])
            ->andReturn(['id' => 4321]);

        $this->mockEntity('OrganisationNatureOfBusiness', 'getAllForOrganisation')
            ->with(12)
            ->andReturn(
                [
                    [
                    'id' => 1,
                    'version' => 1,
                    'organisation' => ['id' => 1],
                    'refData' => ['id' => '1', 'description' => 'desc1']
                    ]
                ]
            );

        $this->mockEntity('OrganisationNatureOfBusiness', 'deleteByOrganisationAndIds')
            ->with(12, [])
            ->andReturn();

        $this->mockEntity('OrganisationNatureOfBusiness', 'save')->with(
            [
                    'organisation' => 12,
                    'refData' => 2,
                    'createdBy' => ''
            ]
        )
        ->andReturn();

        $this->sut->shouldReceive('getCrudAction')
            ->andReturn('add');

        $this->sut
            ->shouldReceive('handleCrudAction')
            ->with('add')
            ->andReturn('crud');

        $this->assertEquals(
            'crud',
            $this->sut->indexAction()
        );
    }

    public function testPostWithValidDataAndCrudAction()
    {
        $form = $this->createMockForm('Lva\BusinessDetails');

        $this->mockOrgData('org_t_rc');

        $postData = [
            'version' => 1,
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'name' => 'Company Name',
                'natureOfBusiness' => [1]
            ],
        ];
        $this->setPost($postData);

        $element = m::mock()->shouldReceive('setOptions')
            ->shouldReceive('getOptions')
            ->andReturn([])
            ->getMock();

        $typeElement = m::mock()->shouldReceive('setValue')
            ->with('org_t_rc')
            ->getMock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('editBusinessType')
            ->andReturn($element)
            ->shouldReceive('get')
            ->with('type')
            ->andReturn($typeElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $form->shouldReceive('has')
            ->with('table')
            ->andReturn(false);

        $form->shouldReceive('setData')
            ->shouldReceive('isValid')
            ->andReturn(true);

        $this->mockEntity('Organisation', 'save')
            ->with(
                [
                    'version' => 1,
                    'companyOrLlpNo' => '12345678',
                    'name' => 'Company Name',
                    'id' => 12
                ]
            );

        $this->mockEntity('OrganisationNatureOfBusiness', 'save')
            ->with(['foo' => 'bar'])
            ->andReturn(['id' => 4321]);

        $this->mockEntity('OrganisationNatureOfBusiness', 'getAllForOrganisation')
            ->with(12)
            ->andReturn(
                [
                    [
                    'id' => 1,
                    'version' => 1,
                    'organisation' => ['id' => 1],
                    'refData' => ['id' => '1', 'description' => 'desc1']
                    ]
                ]
            );

        $this->mockEntity('OrganisationNatureOfBusiness', 'deleteByOrganisationAndIds')
            ->with(12, [])
            ->andReturn();

        $this->sut
            ->shouldReceive('postSave')
            ->with('business_details')
            ->shouldReceive('completeSection')
            ->with('business_details')
            ->andReturn('complete');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }

    public function testGetAdd()
    {
        $form = $this->createMockForm('Lva\BusinessDetailsSubsidiaryCompany');

        $form->shouldReceive('setData')
            ->with([]);

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->mockRender();

        $this->sut->addAction();
    }

    public function testPostAdd()
    {
        $form = $this->createMockForm('Lva\BusinessDetailsSubsidiaryCompany');

        $postData = [
            'data' => ['foo' => 'bar']
        ];
        $this->setPost($postData);

        $form->shouldReceive('setData')
            ->with($postData)
            ->andReturn($form)
            ->shouldReceive('isValid')
            ->andReturn(true);

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->setService(
            'Entity\CompanySubsidiary',
            m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'foo' => 'bar',
                    'organisation' => 12
                ]
            )
            ->getMock()
        );

        $this->sut->shouldReceive('handlePostSave')
            ->andReturn('post-save');

        $this->assertEquals(
            'post-save',
            $this->sut->addAction()
        );
    }

    public function testGetEdit()
    {
        $form = $this->createMockForm('Lva\BusinessDetailsSubsidiaryCompany');

        $form->shouldReceive('setData')
            ->with(
                [
                    'data' => ['bar' => 'foo']
                ]
            )
            ->andReturn($form);

        $form->shouldReceive('get->remove')->with('addAnother');

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->mockRender();

        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn(5050);

        $this->setService(
            'Entity\CompanySubsidiary',
            m::mock()
            ->shouldReceive('getById')
            ->with(5050)
            ->andReturn(['bar' => 'foo'])
            ->getMock()
        );

        $this->sut->editAction();
    }

    private function mockOrgData($type)
    {
        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->mockEntity('Organisation', 'getBusinessDetailsData')
            ->with(12)
            ->andReturn(
                [
                    'version' => 1,
                    'tradingNames' => [
                        ['name' => 'tn 1']
                    ],
                    'companyOrLlpNo' => '12345678',
                    'name' => 'An Org',
                    'type' => [
                        'id' => $type
                    ],
                    'contactDetails' => [
                        'contactType' => [
                            'id' => 'ct_reg'
                        ],
                        'address' => [
                            'foo' => 'bar'
                        ]
                    ]
                ]
            );

        $this->mockEntity('OrganisationNatureOfBusiness', 'getAllForOrganisationForSelect')
            ->with(12)
            ->andReturn([1]);
    }
}
