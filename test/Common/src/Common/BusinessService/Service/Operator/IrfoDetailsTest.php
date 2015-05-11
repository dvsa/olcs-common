<?php

/**
 * IRFO Details Test
 */
namespace CommonTest\BusinessService\Service\Cases\Complaint;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Operator\IrfoDetails;
use Common\BusinessService\Response;

/**
 * IRFO Details Test
 */
class IrfoDetailsTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new IrfoDetails();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    /**
     * @dataProvider processDataProvider
     */
    public function testProcess(
        $params,
        $expectedContactDetailsData,
        $expectedPhoneContactData,
        $expectedOrganisationData,
        $expectedTradingNamesData
    ) {
        // Data
        $irfoContactDetailsId = 1;

        // Mocks
        $mockContactDetailsService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\ContactDetails', $mockContactDetailsService);

        $mockPhoneContactService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\PhoneContact', $mockPhoneContactService);

        $mockOrganisationEntity = m::mock('Common\Service\Data\Interfaces\Updateable');
        $this->sm->setService('Entity\Organisation', $mockOrganisationEntity);

        $mockTradingNamesService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\TradingNames', $mockTradingNamesService);

        // Expectations
        $mockContactDetailsResponse = m::mock('\Common\BusinessService\Response');
        $mockContactDetailsResponse->shouldReceive('isOk')
            ->once()
            ->andReturn(true);
        $mockContactDetailsResponse->shouldReceive('getData')
            ->once()
            ->andReturn(['id' => $irfoContactDetailsId]);

        $mockContactDetailsService->shouldReceive('process')
            ->once()
            ->with($expectedContactDetailsData)
            ->andReturn($mockContactDetailsResponse);

        $mockPhoneContactResponse = m::mock('\Common\BusinessService\Response');
        $mockPhoneContactResponse->shouldReceive('isOk')
            ->once()
            ->andReturn(true);

        $mockPhoneContactService->shouldReceive('process')
            ->once()
            ->with($expectedPhoneContactData)
            ->andReturn($mockPhoneContactResponse);

        $mockOrganisationEntity->shouldReceive('save')
            ->once()
            ->with($expectedOrganisationData)
            ->andReturn(true);

        $mockTradingNamesResponse = m::mock('\Common\BusinessService\Response');
        $mockTradingNamesResponse->shouldReceive('isOk')
            ->times(!empty($expectedTradingNamesData) ? 1 : 0)
            ->andReturn(true);

        $mockTradingNamesService->shouldReceive('process')
            ->times(!empty($expectedTradingNamesData) ? 1 : 0)
            ->with($expectedTradingNamesData)
            ->andReturn($mockTradingNamesResponse);

        $response = $this->sut->process($params);

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }

    public function processDataProvider()
    {
        $irfoContactDetailsId = 1;

        return [
            // add
            [
                // params
                [
                    'id' => 999,
                    'data' => [
                        'irfoNationality' => 'AF',
                    ],
                    'contact' => [
                        'email' => 'email@test.me'
                    ],
                    'address' => 'someAddress',
                ],
                // expected Contact Details data
                [
                    'data' => [
                        'contactType' => 'ct_irfo_op',
                        'address' => 'someAddress',
                        'emailAddress' => 'email@test.me',
                        'phoneContacts' => null
                    ],
                ],
                // expected Phone Contact data
                [
                    'data' => [
                        'contact' => [
                            'email' => 'email@test.me'
                        ],
                    ],
                    'correspondenceId' => $irfoContactDetailsId
                ],
                // expected Organisation data
                [
                    'irfoNationality' => 'AF',
                    'irfoContactDetails' => $irfoContactDetailsId,
                    'tradingNames' => null
                ],
                // expected Trading Names data
                [
                ],
            ],
            // edit
            [
                // params
                [
                    'id' => 999,
                    'data' => [
                        'irfoNationality' => 'AF',
                        'irfoContactDetails' => [
                            'id' => $irfoContactDetailsId,
                            'address' => 'old address',
                            'emailAddress' => 'old@test.me',
                            'phoneContacts' => [
                                ['id' => 200]
                            ]
                        ],
                        'tradingNames' => [
                            ['id' => 1, 'name' => 'tn1'],
                            ['id' => 2, 'name' => 'tn2'],
                            ['id' => 3, 'name' => 'tn3'],
                        ]
                    ],
                    'contact' => [
                        'email' => 'email@test.me'
                    ],
                    'address' => 'someAddress',
                ],
                // expected Contact Details data
                [
                    'data' => [
                        'id' => $irfoContactDetailsId,
                        'contactType' => 'ct_irfo_op',
                        'address' => 'someAddress',
                        'emailAddress' => 'email@test.me',
                        'phoneContacts' => null
                    ],
                ],
                // expected Phone Contact data
                [
                    'data' => [
                        'contact' => [
                            'email' => 'email@test.me'
                        ],
                    ],
                    'correspondenceId' => $irfoContactDetailsId
                ],
                // expected Organisation data
                [
                    'irfoNationality' => 'AF',
                    'irfoContactDetails' => $irfoContactDetailsId,
                    'tradingNames' => null
                ],
                // expected Trading Names data
                [
                    'orgId' => 999,
                    'tradingNames' => ['tn1', 'tn2', 'tn3']
                ],
            ],
        ];
    }
}
