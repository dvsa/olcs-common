<?php

/**
 * Phone Contact Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\PhoneContact;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Phone Contact Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PhoneContactTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new PhoneContact();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        $data = [
            'contact' => [
                // existing but no data; delete
                'phone_business_id' => 10,
                'phone_business_version' => 1,
                // existing with data, update
                'phone_home_id' => 20,
                'phone_home_version' => 1,
                'phone_home' => '0113 123 1234',
                // new with no data; no action
                'phone_mobile_id' => null,
                'phone_mobile_version' => null,
                'phone_mobile' => null,
                // new with no data; no action
                'phone_fax_id' => null,
                'phone_fax_version' => null,
                'phone_fax' => null
            ]
        ];

        $saveData = [
            'id' => 20,
            'version' => 1,
            'phoneNumber' => '0113 123 1234',
            'phoneContactType' => 'phone_t_home',
            'contactDetails' => 4
        ];

        $this->sm->setService(
            'Entity\PhoneContact',
            m::mock()
            ->shouldReceive('save')
            ->with($saveData)
            ->shouldReceive('delete')
            ->with(10)
            ->getMock()
        );

        $response = $this->sut->process(
            [
                'data' => $data,
                'correspondenceId' => 4
            ]
        );

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals([], $response->getData());
    }

    /**
     * @dataProvider mapPhoneFieldsFromDbDataProvider
     */
    public function testMapPhoneFieldsFromDb($data, $expected)
    {
        $this->assertEquals($expected, $this->sut->mapPhoneFieldsFromDb($data));
    }

    public function mapPhoneFieldsFromDbDataProvider()
    {
        return [
            // empty array
            [
                [],
                []
            ],
            // phone business
            [
                [
                    [
                        'id' => 1,
                        'phoneNumber' => '111111',
                        'phoneContactType' => ['id' => 'phone_t_tel'],
                        'version' => 1,
                    ]
                ],
                [
                    'phone_business' => '111111',
                    'phone_business_id' => 1,
                    'phone_business_version' => 1,
                ]
            ],
            // phone home
            [
                [
                    [
                        'id' => 2,
                        'phoneNumber' => '222222',
                        'phoneContactType' => ['id' => 'phone_t_home'],
                        'version' => 2,
                    ]
                ],
                [
                    'phone_home' => '222222',
                    'phone_home_id' => 2,
                    'phone_home_version' => 2,
                ]
            ],
            // phone mobile
            [
                [
                    [
                        'id' => 3,
                        'phoneNumber' => '333333',
                        'phoneContactType' => ['id' => 'phone_t_mobile'],
                        'version' => 3,
                    ]
                ],
                [
                    'phone_mobile' => '333333',
                    'phone_mobile_id' => 3,
                    'phone_mobile_version' => 3,
                ]
            ],
            // phone fax
            [
                [
                    [
                        'id' => 4,
                        'phoneNumber' => '444444',
                        'phoneContactType' => ['id' => 'phone_t_fax'],
                        'version' => 4,
                    ]
                ],
                [
                    'phone_fax' => '444444',
                    'phone_fax_id' => 4,
                    'phone_fax_version' => 4,
                ]
            ],
            // multiple phone records
            [
                [
                    [
                        'id' => 1,
                        'phoneNumber' => '111111',
                        'phoneContactType' => ['id' => 'phone_t_tel'],
                        'version' => 1,
                    ],
                    [
                        'id' => 4,
                        'phoneNumber' => '444444',
                        'phoneContactType' => ['id' => 'phone_t_fax'],
                        'version' => 4,
                    ]
                ],
                [
                    'phone_business' => '111111',
                    'phone_business_id' => 1,
                    'phone_business_version' => 1,
                    'phone_fax' => '444444',
                    'phone_fax_id' => 4,
                    'phone_fax_version' => 4,
                ]
            ],
        ];
    }
}
