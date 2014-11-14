<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractAddressesControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractAddressesController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\Addresses');

        $form->shouldReceive('setData')
            //->with([]) <-- maybe?
            ->andReturn($form);

        $this->sut
            ->shouldReceive('getLicenceId')
            ->andReturn(7);

        $this->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('getAddressesData')
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
            )
            ->getMock()
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

        $this->mockRender();

        $this->sut->indexAction();
    }
}
