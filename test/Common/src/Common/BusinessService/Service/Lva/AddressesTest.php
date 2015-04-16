<?php

/**
 * Addresses Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\Addresses;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Addresses Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AddressesTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new Addresses();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcessWithBadDirtyAddressResponse()
    {
        $data = [
            'originalData' => 'foo',
            'data' => 'bar'
        ];

        $dirtyData = [
            'original' => 'foo',
            'updated' => 'bar'
        ];

        $badResponse = new Response();
        $badResponse->setType(Response::TYPE_FAILED);

        $this->bsm->shouldReceive('get')
            ->with('Lva\DirtyAddresses')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->with($dirtyData)
                ->andReturn($badResponse)
                ->getMock()
            );

        $response = $this->sut->process($data);

        // make sure the sub response is proxied straight through
        $this->assertEquals($response, $badResponse);
        $this->assertEquals(Response::TYPE_FAILED, $response->getType());
        $this->assertEquals([], $response->getData());
    }

    public function testProcessWithBadCorrespondenceData()
    {
        $data = [
            'correspondence' => [
                'id' => 1,
                'version' => 2,
                'fao' => 'A Person'
            ],
            'correspondence_address' => [
                'foo' => 'bar'
            ],
            'contact' => [
                'email' => 'test@user.com'
            ]
        ];

        $processData = [
            'id' => 1,
            'version' => 2,
            'contactType' => 'ct_corr',
            'addresses' => [
                'address' => [
                    'foo' => 'bar'
                ]
            ],
            'fao' => 'A Person',
            'emailAddress' => 'test@user.com'
        ];

        $dirtyData = [
            'original' => 'foo',
            'updated' => $data
        ];

        $dirtyResponse = new Response();
        $dirtyResponse->setType(Response::TYPE_SUCCESS);
        $dirtyResponse->setData(['dirtyFieldsets' => 1]);

        $badResponse = new Response();
        $badResponse->setType(Response::TYPE_FAILED);

        $this->bsm->shouldReceive('get')
            ->with('Lva\DirtyAddresses')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->once()
                ->with($dirtyData)
                ->andReturn($dirtyResponse)
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Lva\ContactDetails')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->with(
                    [
                        'data' => $processData
                    ]
                )
                ->andReturn($badResponse)
                ->getMock()
            );

        $response = $this->sut->process(
            [
                'data' => $data,
                'licenceId' => 123,
                'originalData' => 'foo'
            ]
        );

        // make sure the sub response is proxied straight through
        $this->assertEquals($response, $badResponse);
        $this->assertEquals(Response::TYPE_FAILED, $response->getType());
        $this->assertEquals([], $response->getData());
    }

    public function testProcessWithBadPhoneContactData()
    {
        $data = [
            'correspondence' => [
                'id' => '',
                'version' => '',
                'fao' => 'A Person'
            ],
            'correspondence_address' => [
                'foo' => 'bar'
            ],
            'contact' => [
                'email' => 'test@user.com'
            ]
        ];

        $processData = [
            'id' => '',
            'version' => '',
            'contactType' => 'ct_corr',
            'addresses' => [
                'address' => [
                    'foo' => 'bar'
                ]
            ],
            'fao' => 'A Person',
            'emailAddress' => 'test@user.com'
        ];

        $dirtyData = [
            'original' => 'foo',
            'updated' => $data
        ];

        $dirtyResponse = new Response();
        $dirtyResponse->setType(Response::TYPE_SUCCESS);
        $dirtyResponse->setData(['dirtyFieldsets' => 1]);

        $subResponse = new Response();
        $subResponse->setType(Response::TYPE_SUCCESS);
        $subResponse->setData(
            [
                'id' => 321
            ]
        );

        $this->bsm->shouldReceive('get')
            ->with('Lva\DirtyAddresses')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->once()
                ->with($dirtyData)
                ->andReturn($dirtyResponse)
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Lva\ContactDetails')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->with(
                    [
                        'data' => $processData
                    ]
                )
                ->andReturn($subResponse)
                ->getMock()
            );

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
            ->shouldReceive('forceUpdate')
            ->with(123, ['correspondenceCd' => 321])
            ->getMock()
        );

        $badResponse = new Response();
        $badResponse->setType(Response::TYPE_FAILED);

        $phoneData = [
            'data' => $data,
            'correspondenceId' => 321
        ];

        $this->bsm->shouldReceive('get')
            ->with('Lva\PhoneContact')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->with($phoneData)
                ->andReturn($badResponse)
                ->getMock()
            );

        $response = $this->sut->process(
            [
                'data' => $data,
                'licenceId' => 123,
                'originalData' => 'foo'
            ]
        );

        // make sure the sub response is proxied straight through
        $this->assertEquals($response, $badResponse);
        $this->assertEquals(Response::TYPE_FAILED, $response->getType());
        $this->assertEquals([], $response->getData());
    }

    public function testProcessWithBadEstablishmentData()
    {
        $data = [
            'correspondence' => [
                'id' => 4,
                'version' => 4,
                'fao' => 'A Person'
            ],
            'correspondence_address' => [
                'foo' => 'bar'
            ],
            'contact' => [
                'email' => 'test@user.com'
            ],
            'establishment' => [
                'id' => 10,
                'version' => 1
            ],
            'establishment_address' => [
                'baz' => 'test'
            ],
        ];

        $processData = [
            'id' => 4,
            'version' => 4,
            'contactType' => 'ct_corr',
            'addresses' => [
                'address' => [
                    'foo' => 'bar'
                ]
            ],
            'fao' => 'A Person',
            'emailAddress' => 'test@user.com'
        ];

        $establishmentData = [
            'id' => 10,
            'version' => 1,
            'contactType' => 'ct_est',
            'addresses' => [
                'address' => [
                    'baz' => 'test'
                ]
            ]
        ];

        $dirtyData = [
            'original' => 'foo',
            'updated' => $data
        ];

        $dirtyResponse = new Response();
        $dirtyResponse->setType(Response::TYPE_SUCCESS);
        $dirtyResponse->setData(['dirtyFieldsets' => 1]);

        $subResponse = new Response();
        $subResponse->setType(Response::TYPE_SUCCESS);
        $subResponse->setData(
            [
                'id' => 321
            ]
        );

        $badResponse = new Response();
        $badResponse->setType(Response::TYPE_FAILED);

        $this->bsm->shouldReceive('get')
            ->with('Lva\DirtyAddresses')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->once()
                ->with($dirtyData)
                ->andReturn($dirtyResponse)
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Lva\ContactDetails')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->with(
                    [
                        'data' => $processData
                    ]
                )
                ->andReturn($subResponse)
                ->shouldReceive('process')
                ->with(
                    [
                        'data' => $establishmentData
                    ]
                )
                ->andReturn($badResponse)
                ->getMock()
            );

        $phoneResponse = new Response();
        $phoneResponse->setType(Response::TYPE_SUCCESS);

        $phoneData = [
            'data' => $data,
            'correspondenceId' => 321
        ];

        $this->bsm->shouldReceive('get')
            ->with('Lva\PhoneContact')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->with($phoneData)
                ->andReturn($phoneResponse)
                ->getMock()
            );

        $response = $this->sut->process(
            [
                'data' => $data,
                'licenceId' => 123,
                'originalData' => 'foo'
            ]
        );

        // make sure the sub response is proxied straight through
        $this->assertEquals($response, $badResponse);
        $this->assertEquals(Response::TYPE_FAILED, $response->getType());
        $this->assertEquals([], $response->getData());
    }

    public function testProcessWithEstablishmentDataAndSuccessfulResponse()
    {
        $data = [
            'correspondence' => [
                'id' => 4,
                'version' => 4,
                'fao' => 'A Person'
            ],
            'correspondence_address' => [
                'foo' => 'bar'
            ],
            'contact' => [
                'email' => 'test@user.com'
            ],
            'establishment' => [
                'id' => 10,
                'version' => 1
            ],
            'establishment_address' => [
                'baz' => 'test'
            ],
        ];

        $processData = [
            'id' => 4,
            'version' => 4,
            'contactType' => 'ct_corr',
            'addresses' => [
                'address' => [
                    'foo' => 'bar'
                ]
            ],
            'fao' => 'A Person',
            'emailAddress' => 'test@user.com'
        ];

        $establishmentData = [
            'id' => 10,
            'version' => 1,
            'contactType' => 'ct_est',
            'addresses' => [
                'address' => [
                    'baz' => 'test'
                ]
            ]
        ];

        $dirtyData = [
            'original' => 'foo',
            'updated' => $data
        ];

        $dirtyResponse = new Response();
        $dirtyResponse->setType(Response::TYPE_SUCCESS);
        $dirtyResponse->setData(['dirtyFieldsets' => 1]);

        $subResponse = new Response();
        $subResponse->setType(Response::TYPE_SUCCESS);
        $subResponse->setData(
            [
                'id' => 321
            ]
        );

        $estResponse = new Response();
        $estResponse->setType(Response::TYPE_SUCCESS);

        $this->bsm->shouldReceive('get')
            ->with('Lva\ContactDetails')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->with(
                    [
                        'data' => $processData
                    ]
                )
                ->andReturn($subResponse)
                ->shouldReceive('process')
                ->with(
                    [
                        'data' => $establishmentData
                    ]
                )
                ->andReturn($estResponse)
                ->getMock()
            );

        $phoneResponse = new Response();
        $phoneResponse->setType(Response::TYPE_SUCCESS);

        $phoneData = [
            'data' => $data,
            'correspondenceId' => 321
        ];

        $this->bsm->shouldReceive('get')
            ->with('Lva\DirtyAddresses')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->once()
                ->with($dirtyData)
                ->andReturn($dirtyResponse)
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Lva\PhoneContact')
            ->andReturn(
                m::mock()
                ->shouldReceive('process')
                ->with($phoneData)
                ->andReturn($phoneResponse)
                ->getMock()
            );

        $response = $this->sut->process(
            [
                'data' => $data,
                'licenceId' => 123,
                'originalData' => 'foo'
            ]
        );

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['hasChanged' => true], $response->getData());
    }
}
