<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

/**
 * Test Abstract Addresses Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractAddressesControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractAddressesController');
    }

    public function testGetIndexAction()
    {
        $this->markTestSkipped();

        $form = m::mock('\Common\Form\Form');

        $this->setService(
            'FormServiceManager',
            m::mock()
            ->shouldReceive('get')
            ->andReturn(
                m::mock()
                ->shouldReceive('getForm')
                ->with('ltyp_sn')
                ->andReturn($form)
                ->getMock()
            )
            ->getMock()
        );

        $form->shouldReceive('setData')
            ->andReturn($form);

        $this->sut
            ->shouldReceive('getLicenceId')
            ->andReturn(7);

        $this->mockEntity('Licence', 'getAddressesData')
            ->with(7)
            ->andReturn(
                [
                    'contactDetails' => [],
                    'organisation' => [
                        'contactDetails' => [
                            [
                                'contactType' => [
                                    'id' => 'ct_corr'
                                ],
                                'id' => 123,
                                'version' => 1,
                                'fao' => 'FAO',
                                'address' => [
                                    'countryCode' => [
                                        'id' => 'GB'
                                    ]
                                ],
                                'emailAddress' => 'foo@bar.com',
                                'phoneContacts' => [
                                    [
                                        'phoneContactType' => [
                                            'id' => 'phone_t_tel'
                                        ],
                                        'phoneNumber' => '0123 123 1234',
                                        'id' => 321,
                                        'version' => 1
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

        $this->sut
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'version' => 1,
                    'niFlag' => 'x',
                    'goodsOrPsv' => 'y',
                    'licenceType' => 'ltyp_sn'
                ]
            );

        $this->getMockFormHelper()
            ->shouldReceive('processAddressLookupForm')
            ->andReturn(false);

        $this->setService(
            'Script',
            m::mock()
                ->shouldReceive('loadFiles')
                ->with(['forms/addresses'])
                ->getMock()
        );

        $this->mockRender();

        $this->assertEquals('addresses', $this->sut->indexAction());
    }

    public function testPostIndexAction()
    {
        $this->markTestSkipped();

        $this->setPost(
            array(
                'consultant' => array(
                    'add-transport-consultant' => 'N'
                )
            )
        );

        $form = m::mock('\Common\Form\Form');

        $this->setService(
            'FormServiceManager',
            m::mock()
                ->shouldReceive('get')
                ->andReturn(
                    m::mock()
                        ->shouldReceive('getForm')
                        ->with('ltyp_sn')
                        ->andReturn($form)
                        ->getMock()
                )
                ->getMock()
        );

        $form->shouldReceive('setData')
            ->andReturn($form)
            ->shouldReceive('getInputFilter')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('consultant')
                    ->getMock()
            )->shouldReceive('isValid')
            ->andReturn(true);

        $this->sut
            ->shouldReceive('getLicenceId')
            ->andReturn(7);

        $this->mockEntity('Licence', 'getAddressesData')
            ->with(7)
            ->andReturn(
                [
                    'correspondenceCd' => [
                        'id' => 1,
                        'version' => 1,
                        'fao' => 'Test',
                        'emailAddress' => 'test@test.com',
                        'address' => [
                            'countryCode' => [
                                'id' => 'GB'
                            ]
                        ],
                        'phoneContacts' => [
                            [
                                'phoneContactType' => [
                                    'id' => 'phone_t_tel'
                                ],
                                'phoneNumber' => '0123 123 1234',
                                'id' => 321,
                                'version' => 1
                            ]
                        ]
                    ],
                    'transportConsultantCd' => [
                        'writtenPermissionToEngage' => 'Y',
                        'fao' => 'Test',
                        'address' => [],
                        'phoneContacts' => [
                            [
                                'phoneContactType' => [
                                    'id' => 'phone_t_tel'
                                ],
                                'phoneNumber' => '0123 123 1234',
                                'id' => 321,
                                'version' => 1
                            ]
                        ]
                    ],
                    'contactDetails' => [],
                    'organisation' => [
                        'contactDetails' => [
                            [
                                'contactType' => [
                                    'id' => 'ct_corr'
                                ],
                                'id' => 123,
                                'version' => 1,
                                'fao' => 'FAO',
                                'address' => [
                                    'countryCode' => [
                                        'id' => 'GB'
                                    ]
                                ],
                                'emailAddress' => 'foo@bar.com',
                                'phoneContacts' => [
                                    [
                                        'phoneContactType' => [
                                            'id' => 'phone_t_tel'
                                        ],
                                        'phoneNumber' => '0123 123 1234',
                                        'id' => 321,
                                        'version' => 1
                                    ]
                                ]
                            ]
                        ]
                    ],
                ]
            );

        $this->setService(
            'BusinessServiceManager',
            m::mock()
                ->shouldReceive('get')
                ->andReturn(
                    m::mock()
                        ->shouldReceive('process')
                        ->andReturn(
                            m::mock()
                                ->shouldReceive('isOk')
                                ->andReturn(true)
                                ->shouldReceive('getData')
                                ->andReturn([])
                                ->getMock()
                        )
                        ->getMock()
                )
                ->getMock()
        );

        $this->sut
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'version' => 1,
                    'licenceType' => 'ltyp_sn'
                ]
            );

        $this->getMockFormHelper()
            ->shouldReceive('processAddressLookupForm')
            ->andReturn(false)
            ->shouldReceive('disableValidation');

        $this->setService(
            'Script',
            m::mock()
                ->shouldReceive('loadFiles')
                ->with(['forms/addresses'])
                ->getMock()
        );

        $this->sut->shouldReceive('completeSection');

        $this->sut->indexAction();
    }
}
