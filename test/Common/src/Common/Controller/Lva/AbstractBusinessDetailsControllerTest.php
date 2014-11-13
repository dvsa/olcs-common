<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

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

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('getBusinessDetailsData')
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
                        'id' => 'org_t_st'
                    ],
                    'contactDetails' => []
                ]
            )
            ->getMock()
        );

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
                        'registeredAddress' => []
                    ]
                ]
            );

        $this->shouldRemoveElements(
            $form,
            [
                'table',
                'data->companyNumber',
                'data->registeredAddress',
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

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('getBusinessDetailsData')
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
                        'id' => 'org_t_rc'
                    ],
                    'contactDetails' => []
                ]
            )
            ->getMock()
        );

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
                        'registeredAddress' => []
                    ]
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

        $this->setService(
            'Entity\CompanySubsidiary',
            m::mock()
            ->shouldReceive('getAllForOrganisation')
            ->with(12)
            ->andReturn([])
            ->getMock()
        );

        $mockTable = m::mock();

        $this->setService(
            'Table',
            m::mock()
            ->shouldReceive('prepareTable')
            ->with('lva-subsidiaries', [])
            ->andReturn($mockTable)
            ->getMock()
        );

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

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('getBusinessDetailsData')
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
                        'id' => 'org_t_pa'
                    ],
                    'contactDetails' => []
                ]
            )
            ->getMock()
        );

        $this->shouldRemoveElements(
            $form,
            [
                'data->tradingNames',
                'table',
                'data->companyNumber',
                'data->registeredAddress',
                'data->name'
            ]
        );

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
                        'registeredAddress' => []
                    ]
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

        $this->sut->indexAction();

        $this->assertEquals('business_details', $this->view);
    }

    public function testPostWithCompanyNumberLookup()
    {
        $form = $this->createMockForm('Lva\BusinessDetails');

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->setService(
            'Entity\Organisation',
            m::mock()
            ->shouldReceive('getBusinessDetailsData')
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
                        'id' => 'org_t_rc'
                    ],
                    'contactDetails' => []
                ]
            )
            ->getMock()
        );

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

    /*
    public function testPostWithInvalidData()
    {
        $this->mockRender();

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->setPost();

        $this->sut->indexAction();

        $this->assertEquals('business_type', $this->view);
    }

    public function testPostWithValidData()
    {
        $this->disableCsrf();

        $this->sut
            ->shouldReceive('getCurrentOrganisationId')
            ->andReturn(12);

        $this->setPost(
            [
                'version' => 1,
                'data' => [
                    'type' => 'org_t_rc'
                ]
            ]
        );

        $oEntity = m::mock()
            ->shouldReceive('save')
            ->with(
                [
                    'version' => 1,
                    'id' => 12,
                    'type' => 'org_t_rc'
                ]
            )
            ->getMock();

        $this->setService('Entity\Organisation', $oEntity);

        $this->sut
            ->shouldReceive('postSave')
            ->with('business_type')
            ->shouldReceive('completeSection')
            ->with('business_type')
            ->andReturn('complete');

        $this->assertEquals(
            'complete',
            $this->sut->indexAction()
        );
    }
     */
}
