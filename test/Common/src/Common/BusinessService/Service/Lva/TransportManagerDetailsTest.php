<?php

/**
 * Transport Manager Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\TransportManagerDetails;
use Common\BusinessService\Response;
use Common\Service\Entity\ContactDetailsEntityService;
use Common\Service\Entity\TransportManagerApplicationEntityService;

/**
 * Transport Manager Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerDetailsTest extends MockeryTestCase
{
    protected $sut;

    protected $bsm;

    public function setUp()
    {
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new TransportManagerDetails();
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcessHomeCdFail()
    {
        $params = [
            'contactDetails' => [
                'id' => 111,
                'version' => 1
            ],
            'data' => [
                'details' => [
                    'emailAddress' => 'foo@bar.com'
                ],
                'homeAddress' => [
                    'addressLine1' => '123 street'
                ]
            ]
        ];

        // Mocks
        $mockHomeCdResponse = new Response(Response::TYPE_FAILED);
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);

        // Expecations
        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedHomeCdData())
            ->andReturn($mockHomeCdResponse);

        $this->assertSame($mockHomeCdResponse, $this->sut->process($params));
    }

    public function testProcessPersonFail()
    {
        $params = [
            'contactDetails' => [
                'id' => 111,
                'version' => 1
            ],
            'person' => [
                'id' => 222,
                'version' => 1
            ],
            'data' => [
                'details' => [
                    'birthPlace' => 'Hometown',
                    'emailAddress' => 'foo@bar.com'
                ],
                'homeAddress' => [
                    'addressLine1' => '123 street'
                ]
            ]
        ];

        // Mocks
        $mockHomeCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 111]);
        $mockPersonResponse = new Response(Response::TYPE_FAILED);
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockPerson = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);
        $this->bsm->setService('Lva\Person', $mockPerson);

        // Expecations
        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedHomeCdData())
            ->andReturn($mockHomeCdResponse);

        $mockPerson->shouldReceive('process')
            ->with($this->getExpectedPersonData())
            ->andReturn($mockPersonResponse);

        $this->assertSame($mockPersonResponse, $this->sut->process($params));
    }

    public function testProcessWorkCdUpdateFail()
    {
        $params = [
            'contactDetails' => [
                'id' => 111,
                'version' => 1
            ],
            'person' => [
                'id' => 222,
                'version' => 1
            ],
            'workContactDetails' => [
                'id' => 333,
                'version' => 1
            ],
            'data' => [
                'details' => [
                    'birthPlace' => 'Hometown',
                    'emailAddress' => 'foo@bar.com'
                ],
                'homeAddress' => [
                    'addressLine1' => '123 street'
                ],
                'workAddress' => [
                    'addressLine1' => '123 work street'
                ]
            ]
        ];

        // Mocks
        $mockHomeCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 111]);
        $mockPersonResponse = new Response(Response::TYPE_SUCCESS, ['id' => 222]);
        $mockWorkCdResponse = new Response(Response::TYPE_FAILED);
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockPerson = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);
        $this->bsm->setService('Lva\Person', $mockPerson);

        // Expecations
        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedHomeCdData())
            ->andReturn($mockHomeCdResponse);

        $mockPerson->shouldReceive('process')
            ->with($this->getExpectedPersonData())
            ->andReturn($mockPersonResponse);

        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedWorkCdUpdateData())
            ->andReturn($mockWorkCdResponse);

        $this->assertSame($mockWorkCdResponse, $this->sut->process($params));
    }

    public function testProcessWorkCdCreateFail()
    {
        $params = [
            'contactDetails' => [
                'id' => 111,
                'version' => 1
            ],
            'person' => [
                'id' => 222,
                'version' => 1
            ],
            'workContactDetails' => [
                'id' => null,
                'version' => null
            ],
            'data' => [
                'details' => [
                    'birthPlace' => 'Hometown',
                    'emailAddress' => 'foo@bar.com'
                ],
                'homeAddress' => [
                    'addressLine1' => '123 street'
                ],
                'workAddress' => [
                    'addressLine1' => '123 work street'
                ]
            ]
        ];

        // Mocks
        $mockHomeCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 111]);
        $mockPersonResponse = new Response(Response::TYPE_SUCCESS, ['id' => 222]);
        $mockWorkCdResponse = new Response(Response::TYPE_FAILED);
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockPerson = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);
        $this->bsm->setService('Lva\Person', $mockPerson);

        // Expecations
        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedHomeCdData())
            ->andReturn($mockHomeCdResponse);

        $mockPerson->shouldReceive('process')
            ->with($this->getExpectedPersonData())
            ->andReturn($mockPersonResponse);

        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedWorkCdCreateData())
            ->andReturn($mockWorkCdResponse);

        $this->assertSame($mockWorkCdResponse, $this->sut->process($params));
    }

    public function testProcessWorkCdCreateSuccessTmFail()
    {
        $params = [
            'contactDetails' => [
                'id' => 111,
                'version' => 1
            ],
            'person' => [
                'id' => 222,
                'version' => 1
            ],
            'workContactDetails' => [
                'id' => null,
                'version' => null
            ],
            'transportManager' => [
                'id' => 444,
                'version' => 1
            ],
            'data' => [
                'details' => [
                    'birthPlace' => 'Hometown',
                    'emailAddress' => 'foo@bar.com'
                ],
                'homeAddress' => [
                    'addressLine1' => '123 street'
                ],
                'workAddress' => [
                    'addressLine1' => '123 work street'
                ]
            ]
        ];

        // Mocks
        $mockHomeCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 111]);
        $mockPersonResponse = new Response(Response::TYPE_SUCCESS, ['id' => 222]);
        $mockWorkCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 333]);
        $mockTmResponse = new Response(Response::TYPE_FAILED);
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockPerson = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockTm = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);
        $this->bsm->setService('Lva\Person', $mockPerson);
        $this->bsm->setService('Lva\TransportManager', $mockTm);

        // Expecations
        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedHomeCdData())
            ->andReturn($mockHomeCdResponse);

        $mockPerson->shouldReceive('process')
            ->with($this->getExpectedPersonData())
            ->andReturn($mockPersonResponse);

        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedWorkCdCreateData())
            ->andReturn($mockWorkCdResponse);

        $mockTm->shouldReceive('process')
            ->with($this->getExpectedTmData())
            ->andReturn($mockTmResponse);

        $this->assertSame($mockTmResponse, $this->sut->process($params));
    }

    public function testProcessWorkCdUpdateSuccess()
    {
        $params = [
            'submit' => false,
            'contactDetails' => [
                'id' => 111,
                'version' => 1
            ],
            'person' => [
                'id' => 222,
                'version' => 1
            ],
            'workContactDetails' => [
                'id' => 333,
                'version' => 1
            ],
            'data' => [
                'details' => [
                    'birthPlace' => 'Hometown',
                    'emailAddress' => 'foo@bar.com'
                ],
                'homeAddress' => [
                    'addressLine1' => '123 street'
                ],
                'workAddress' => [
                    'addressLine1' => '123 work street'
                ]
            ]
        ];

        $expectedResponseData = [
            'contactDetailsId' => 111,
            'personId' => 222,
            'workContactDetailsId' => 333
        ];

        // Mocks
        $mockHomeCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 111]);
        $mockPersonResponse = new Response(Response::TYPE_SUCCESS, ['id' => 222]);
        $mockWorkCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 333]);
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockPerson = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);
        $this->bsm->setService('Lva\Person', $mockPerson);

        // Expecations
        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedHomeCdData())
            ->andReturn($mockHomeCdResponse);

        $mockPerson->shouldReceive('process')
            ->with($this->getExpectedPersonData())
            ->andReturn($mockPersonResponse);

        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedWorkCdUpdateData())
            ->andReturn($mockWorkCdResponse);

        $response = $this->sut->process($params);

        $this->assertTrue($response->isOK());
        $this->assertEquals($expectedResponseData, $response->getData());
    }

    public function testProcessWorkCdCreateSuccessTmSuccess()
    {
        $params = [
            'submit' => false,
            'contactDetails' => [
                'id' => 111,
                'version' => 1
            ],
            'person' => [
                'id' => 222,
                'version' => 1
            ],
            'workContactDetails' => [
                'id' => null,
                'version' => null
            ],
            'transportManager' => [
                'id' => 444,
                'version' => 1
            ],
            'data' => [
                'details' => [
                    'birthPlace' => 'Hometown',
                    'emailAddress' => 'foo@bar.com'
                ],
                'homeAddress' => [
                    'addressLine1' => '123 street'
                ],
                'workAddress' => [
                    'addressLine1' => '123 work street'
                ]
            ]
        ];

        $expectedResponseData = [
            'contactDetailsId' => 111,
            'personId' => 222,
            'workContactDetailsId' => 333,
            'transportManagerId' => 444
        ];

        // Mocks
        $mockHomeCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 111]);
        $mockPersonResponse = new Response(Response::TYPE_SUCCESS, ['id' => 222]);
        $mockWorkCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 333]);
        $mockTmResponse = new Response(Response::TYPE_SUCCESS, ['id' => 444]);
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockPerson = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockTm = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);
        $this->bsm->setService('Lva\Person', $mockPerson);
        $this->bsm->setService('Lva\TransportManager', $mockTm);

        // Expecations
        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedHomeCdData())
            ->andReturn($mockHomeCdResponse);

        $mockPerson->shouldReceive('process')
            ->with($this->getExpectedPersonData())
            ->andReturn($mockPersonResponse);

        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedWorkCdCreateData())
            ->andReturn($mockWorkCdResponse);

        $mockTm->shouldReceive('process')
            ->with($this->getExpectedTmData())
            ->andReturn($mockTmResponse);

        $response = $this->sut->process($params);

        $this->assertTrue($response->isOK());
        $this->assertEquals($expectedResponseData, $response->getData());
    }

    public function testProcessSubmitFail()
    {
        $params = [
            'submit' => true,
            'contactDetails' => [
                'id' => 111,
                'version' => 1
            ],
            'person' => [
                'id' => 222,
                'version' => 1
            ],
            'workContactDetails' => [
                'id' => null,
                'version' => null
            ],
            'transportManager' => [
                'id' => 444,
                'version' => 1
            ],
            'transportManagerApplication' => [
                'id' => 555,
                'version' => 1
            ],
            'data' => [
                'details' => [
                    'birthPlace' => 'Hometown',
                    'emailAddress' => 'foo@bar.com'
                ],
                'homeAddress' => [
                    'addressLine1' => '123 street'
                ],
                'workAddress' => [
                    'addressLine1' => '123 work street'
                ]
            ]
        ];

        // Mocks
        $mockHomeCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 111]);
        $mockPersonResponse = new Response(Response::TYPE_SUCCESS, ['id' => 222]);
        $mockWorkCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 333]);
        $mockTmResponse = new Response(Response::TYPE_SUCCESS, ['id' => 444]);
        $mockTmaResponse = new Response(Response::TYPE_FAILED);
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockPerson = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockTm = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockTma = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);
        $this->bsm->setService('Lva\Person', $mockPerson);
        $this->bsm->setService('Lva\TransportManager', $mockTm);
        $this->bsm->setService('Lva\TransportManagerApplication', $mockTma);

        // Expecations
        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedHomeCdData())
            ->andReturn($mockHomeCdResponse);

        $mockPerson->shouldReceive('process')
            ->with($this->getExpectedPersonData())
            ->andReturn($mockPersonResponse);

        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedWorkCdCreateData())
            ->andReturn($mockWorkCdResponse);

        $mockTm->shouldReceive('process')
            ->with($this->getExpectedTmData())
            ->andReturn($mockTmResponse);

        $mockTma->shouldReceive('process')
            ->with($this->getExpectedTmaData())
            ->andReturn($mockTmaResponse);

        $this->assertSame($mockTmaResponse, $this->sut->process($params));
    }

    public function testProcessSubmitSuccess()
    {
        $params = [
            'submit' => true,
            'contactDetails' => [
                'id' => 111,
                'version' => 1
            ],
            'person' => [
                'id' => 222,
                'version' => 1
            ],
            'workContactDetails' => [
                'id' => null,
                'version' => null
            ],
            'transportManager' => [
                'id' => 444,
                'version' => 1
            ],
            'transportManagerApplication' => [
                'id' => 555,
                'version' => 1
            ],
            'data' => [
                'details' => [
                    'birthPlace' => 'Hometown',
                    'emailAddress' => 'foo@bar.com'
                ],
                'homeAddress' => [
                    'addressLine1' => '123 street'
                ],
                'workAddress' => [
                    'addressLine1' => '123 work street'
                ]
            ]
        ];

        $expectedResponseData = [
            'contactDetailsId' => 111,
            'personId' => 222,
            'workContactDetailsId' => 333,
            'transportManagerId' => 444,
            'transportManagerApplicationId' => 555
        ];

        // Mocks
        $mockHomeCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 111]);
        $mockPersonResponse = new Response(Response::TYPE_SUCCESS, ['id' => 222]);
        $mockWorkCdResponse = new Response(Response::TYPE_SUCCESS, ['id' => 333]);
        $mockTmResponse = new Response(Response::TYPE_SUCCESS, ['id' => 444]);
        $mockTmaResponse = new Response(Response::TYPE_SUCCESS, ['id' => 555]);
        $mockContactDetails = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockPerson = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockTm = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockTma = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\ContactDetails', $mockContactDetails);
        $this->bsm->setService('Lva\Person', $mockPerson);
        $this->bsm->setService('Lva\TransportManager', $mockTm);
        $this->bsm->setService('Lva\TransportManagerApplication', $mockTma);

        // Expecations
        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedHomeCdData())
            ->andReturn($mockHomeCdResponse);

        $mockPerson->shouldReceive('process')
            ->with($this->getExpectedPersonData())
            ->andReturn($mockPersonResponse);

        $mockContactDetails->shouldReceive('process')
            ->with($this->getExpectedWorkCdCreateData())
            ->andReturn($mockWorkCdResponse);

        $mockTm->shouldReceive('process')
            ->with($this->getExpectedTmData())
            ->andReturn($mockTmResponse);

        $mockTma->shouldReceive('process')
            ->with($this->getExpectedTmaData())
            ->andReturn($mockTmaResponse);

        $response = $this->sut->process($params);

        $this->assertTrue($response->isOK());
        $this->assertEquals($expectedResponseData, $response->getData());
    }

    protected function getExpectedTmaData()
    {
        return [
            'data' => [
                'id' => 555,
                'version' => 1,
                'tmApplicationStatus' => TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE
            ]
        ];
    }

    protected function getExpectedTmData()
    {
        return [
            'data' => [
                'id' => 444,
                'version' => 1,
                'workCd' => 333
            ]
        ];
    }

    protected function getExpectedWorkCdCreateData()
    {
        return [
            'data' => [
                'contactType' => ContactDetailsEntityService::CONTACT_TYPE_TRANSPORT_MANAGER,
                'addresses' => [
                    'address' => [
                        'addressLine1' => '123 work street'
                    ]
                ]
            ]
        ];
    }

    protected function getExpectedWorkCdUpdateData()
    {
        return [
            'data' => [
                'id' => 333,
                'version' => 1,
                'addresses' => [
                    'address' => [
                        'addressLine1' => '123 work street'
                    ]
                ]
            ]
        ];
    }

    protected function getExpectedPersonData()
    {
        return [
            'data' => [
                'id' => 222,
                'version' => 1,
                'birthPlace' => 'Hometown'
            ]
        ];
    }

    protected function getExpectedHomeCdData()
    {
        return [
            'data' => [
                'id' => 111,
                'version' => 1,
                'emailAddress' => 'foo@bar.com',
                'addresses' => [
                    'address' => [
                        'addressLine1' => '123 street'
                    ]
                ]
            ]
        ];
    }
}
