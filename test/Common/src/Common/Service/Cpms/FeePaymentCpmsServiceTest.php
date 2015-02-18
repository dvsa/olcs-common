<?php

/**
 * CPMS Fee Payment Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Cpms;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Cpms\FeePaymentCpmsService;
use Common\Service\Cpms\PaymentNotFoundException;
use Common\Service\Cpms\PaymentInvalidStatusException;
use Common\Service\Entity\PaymentEntityService;
use Common\Service\Entity\FeeEntityService;
use Common\Service\Entity\FeePaymentEntityService;
use Mockery as m;
use Common\Service\Listener\FeeListenerService;
use CommonTest\Traits\MockDateTrait;
use CommonTest\Bootstrap;

/**
 * CPMS Fee Payment Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeePaymentCpmsServiceTest extends MockeryTestCase
{
    use MockDateTrait;

    protected $sm;

    protected $sut;

    protected $client;

    /**
     * @var \Zend\Log\Writer\Mock
     */
    protected $logWriter;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new FeePaymentCpmsService();
        $this->sut->setServiceLocator($this->sm);
        $this->mockDate('2015-01-21');

        // Mock the logger
        $this->logWriter = new \Zend\Log\Writer\Mock();
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($this->logWriter);
        $this->sut->setLogger($logger);

        $this->client = m::mock()
            ->shouldReceive('getOptions')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getDomain')
                    ->andReturn('fake-domain')
                    ->getMock()
            )
            ->getMock();

        // $this->client = m::mock()
        //     ->shouldReceive('getOptions->getDomain')
        //     ->andReturn('fake-domain')
        //     ->getMock();

        $this->sm->setService('cpms\service\api', $this->client);

        return parent::setUp();
    }

    public function testInitiateCardRequest()
    {
        $this->mockDate('2015-01-12');
        $fees = [
            [
                'id' => 1,
                'amount' => 525.25,
                'feeType' => [
                    'accrualRule' => [
                        'id' => 'acr_immediate',
                        // Common\Service\Data\FeeTypeDataService::ACCRUAL_RULE_IMMEDIATE
                    ]
                ],
            ],
            [
                'id' => 2,
                'amount' => 125.25,
                'feeType' => [
                    'accrualRule' => [
                        'id' => 'acr_licence_start',
                         // Common\Service\Data\FeeTypeDataService::ACCRUAL_RULE_LICENCE_START
                    ]
                ],
                'licence' => ['id' => 7, 'inForceDate' => '2014-12-25'],
            ],
        ];

        $params = [
            'customer_reference' => 'cust_ref',
            'scope' => 'CARD',
            'disable_redirection' => true,
            'redirect_uri' => 'redirect_url',
            'payment_data' => [
                [
                    'amount' => (double)525.25,
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payment_reference' => [
                        'rule_start_date' => '12-01-2015',
                    ],
                ],
                [
                    'amount' => (double)125.25,
                    'sales_reference' => '2',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payment_reference' => [
                        'rule_start_date' => '25-12-2014',
                    ],
                ]
            ],
            'cost_centre' => '12345,67890',
        ];

        $this->client->shouldReceive('post')
            ->with('/api/payment/card', 'CARD', $params)
            ->andReturn(
                [
                    'redirection_data' => 'guid_123'
                ]
            )
            ->getMock();

        $this->sm->setService(
            'Entity\Payment',
            m::mock()
                ->shouldReceive('save')
                ->with(
                    [
                        'guid' => 'guid_123',
                        'status' => PaymentEntityService::STATUS_OUTSTANDING
                    ]
                )
                ->andReturn(
                    [
                        'id' => 321
                    ]
                )
                ->getMock()
        );

        $this->sm->setService(
            'Entity\FeePayment',
            m::mock()
                ->shouldReceive('save')
                ->with(
                    [
                        'payment' => 321,
                        'fee' => 1,
                        'feeValue' => 525.25
                    ]
                )
                ->shouldReceive('save')
                ->with(
                    [
                        'payment' => 321,
                        'fee' => 2,
                        'feeValue' => 125.25
                    ]
                )
                ->getMock()
        );

        $this->sut->initiateCardRequest('cust_ref', 'redirect_url', $fees);

        $this->assertCount(2, $this->logWriter->events);
        $this->assertEquals('Card payment request', $this->logWriter->events[0]['message']);
        $this->assertEquals('Card payment response', $this->logWriter->events[1]['message']);
    }

    /**
     * @expectedException Common\Service\Cpms\PaymentInvalidResponseException
     * @expectedExceptionMessage some kind of error
     */
    public function testInitiateCardRequestWithInvalidResponseThrowsException()
    {
        $this->client->shouldReceive('post')
            ->with('/api/payment/card', 'CARD', m::any())
            ->andReturn('some kind of error')
            ->getMock();

        $fees = [
            [
                'id' => 1,
                'amount' => 525.25
            ]
        ];

        $this->sut->initiateCardRequest('cust_ref', 'redirect_url', $fees);
    }

    public function testHandleResponseWithInvalidPayment()
    {
        $this->sm->setService(
            'Entity\Payment',
            m::mock()
                ->shouldReceive('getDetails')
                ->with('payment_reference')
                ->andReturn(false)
                ->getMock()
        );

        $data = [
            'receipt_reference' => 'payment_reference'
        ];

        try {
            $this->sut->handleResponse($data, 'PAYMENT_METHOD');
        } catch (PaymentNotFoundException $ex) {
            $this->assertEquals('Payment not found', $ex->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }

    public function testHandleResponseWithInvalidPaymentStatus()
    {
        $this->sm->setService(
            'Entity\Payment',
            m::mock()
                ->shouldReceive('getDetails')
                ->with('payment_reference')
                ->andReturn(
                    [
                        'status' => [
                            'id' => 'bad_status'
                        ]
                    ]
                )
                ->getMock()
        );

        $data = [
            'receipt_reference' => 'payment_reference'
        ];

        try {
            $this->sut->handleResponse($data, 'PAYMENT_METHOD');
        } catch (PaymentInvalidStatusException $ex) {
            $this->assertEquals('Invalid payment status: bad_status', $ex->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }

    public function testHandleResponseWithValidPaymentStatus()
    {
        $data = [
            'receipt_reference' => 'payment_reference'
        ];

        $queryData = [
            'required_fields' => [
                'payment' => [
                    'payment_status'
                ]
            ]
        ];
        $this->client->shouldReceive('put')
            ->with('/api/gateway/payment_reference/complete', 'CARD', $data)
            ->shouldReceive('get')
            ->with('/api/payment/payment_reference', 'QUERY_TXN', $queryData)
            ->andReturn(
                [
                    'payment_status' => [
                        'code' => 801
                    ]
                ]
            )
            ->getMock();

        $saveData = [
            'feeStatus' => FeeEntityService::STATUS_PAID,
            'receivedDate' => '2014-12-30 01:20:30',
            'receiptNo' => 'payment_reference',
            'paymentMethod' => 'PAYMENT_METHOD',
            'receivedAmount' => 525.33
        ];

        $mockFeeListener = m::mock();
        $mockFeeListener->shouldReceive('trigger')
            ->with(1, FeeListenerService::EVENT_PAY);

        $this->sm->setService(
            'Entity\Payment',
            m::mock()
                ->shouldReceive('getDetails')
                ->with('payment_reference')
                ->andReturn(
                    [
                        'id' => 123,
                        'status' => [
                            'id' => PaymentEntityService::STATUS_OUTSTANDING
                        ]
                    ]
                )
                ->shouldReceive('setStatus')
                ->with(123, PaymentEntityService::STATUS_PAID)
                ->getMock()
        );

        $this->sm->setService(
            'Helper\Date',
            m::mock()
                ->shouldReceive('getDate')
                ->with('Y-m-d H:i:s')
                ->andReturn('2014-12-30 01:20:30')
                ->getMock()
        );

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('forceUpdate')
                ->with(1, $saveData)
                ->getMock()
        );

        $this->sm->setService('Listener\Fee', $mockFeeListener);

        $fees = [
            [
                'amount' => 525.33,
                'id' => 1
            ]
        ];

        $this->sm->setService(
            'Entity\FeePayment',
            m::mock()
                ->shouldReceive('getFeesByPaymentId')
                ->with(123)
                ->andReturn($fees)
                ->getMock()
        );

        $resultStatus = $this->sut->handleResponse($data, 'PAYMENT_METHOD');

        $this->assertEquals(
            PaymentEntityService::STATUS_PAID, $resultStatus
        );
    }

    /**
     * @dataProvider nonSuccessfulStatusProvider
     */
    public function testHandleResponseWithNonSuccessfulPaymentStatus($code, $status)
    {
        $data = [
            'receipt_reference' => 'payment_reference'
        ];

        $queryData = [
            'required_fields' => [
                'payment' => [
                    'payment_status'
                ]
            ]
        ];
        $this->client->shouldReceive('put')
            ->with('/api/gateway/payment_reference/complete', 'CARD', $data)
            ->shouldReceive('get')
            ->with('/api/payment/payment_reference', 'QUERY_TXN', $queryData)
            ->andReturn(
                [
                    'payment_status' => [
                        'code' => $code
                    ]
                ]
            )
            ->getMock();

        $this->sm->setService(
            'Entity\Payment',
            m::mock()
                ->shouldReceive('getDetails')
                ->with('payment_reference')
                ->andReturn(
                    [
                        'id' => 123,
                        'status' => [
                            'id' => PaymentEntityService::STATUS_OUTSTANDING
                        ]
                    ]
                )
                ->shouldReceive('setStatus')
                ->with(123, $status)
                ->getMock()
        );

        $resultStatus = $this->sut->handleResponse($data, []);

        $this->assertEquals($status, $resultStatus);
    }

    public function nonSuccessfulStatusProvider()
    {
        return [
            [807, PaymentEntityService::STATUS_FAILED],
            [802, PaymentEntityService::STATUS_CANCELLED]
        ];
    }

    public function testHandleResponseWithUnhandledStatus()
    {
        $data = [
            'receipt_reference' => 'payment_reference'
        ];

        $queryData = [
            'required_fields' => [
                'payment' => [
                    'payment_status'
                ]
            ]
        ];
        $this->client->shouldReceive('put')
            ->with('/api/gateway/payment_reference/complete', 'CARD', $data)
            ->shouldReceive('get')
            ->with('/api/payment/payment_reference', 'QUERY_TXN', $queryData)
            ->andReturn(
                [
                    'payment_status' => [
                        'code' => 12345
                    ]
                ]
            )
            ->getMock();

        $this->sm->setService(
            'Entity\Payment',
            m::mock()
                ->shouldReceive('getDetails')
                ->with('payment_reference')
                ->andReturn(
                    [
                        'id' => 123,
                        'status' => [
                            'id' => PaymentEntityService::STATUS_OUTSTANDING
                        ]
                    ]
                )
                ->getMock()
        );

        $resultStatus = $this->sut->handleResponse($data, []);

        $this->assertEquals(null, $resultStatus);
    }

    public function testRecordCashPayment()
    {
        $params = [
            'customer_reference' => 'cust_ref',
            'scope' => 'CASH',
            'total_amount' => (double)1234.56,
            'payment_data' => [
                [
                    'amount' => (double)1234.56,
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payer_details' => 'Payer',
                    'payment_reference' => [
                        'slip_number' => '123456',
                        'receipt_date' => '07-01-2015',
                        'rule_start_date' => null, // tested separately
                    ],
                ]
            ],
            'cost_centre' => '12345,67890',
        ];

        $this->client->shouldReceive('post')
            ->with('/api/payment/cash', 'CASH', $params)
            ->andReturn(
                [
                    'code' => '000',
                    'message' => 'Success',
                    'receipt_reference' => 'unique_reference'
                ]
            )
            ->getMock();

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
            ->shouldReceive('forceUpdate')
            ->with(
                1,
                [
                    'feeStatus'          => 'lfs_pd', //FeeEntityService::STATUS_PAID
                    'receivedDate'       => '07-01-2015',
                    'receiptNo'          => 'unique_reference',
                    'paymentMethod'      => 'fpm_cash', //FeePaymentEntityService::METHOD_CASH
                    'receivedAmount'     => '1234.56',
                    'payerName'          => 'Payer',
                    'payingInSlipNumber' => '123456',
                ]
            )
            ->getMock()
        );
        $this->sm->setService(
            'Listener\Fee',
            m::mock()
            ->shouldReceive('trigger')
            ->with(1, FeeListenerService::EVENT_PAY)
            ->getMock()
        );

        $this->sut->setServiceLocator($this->sm);

        $fee = ['id' => 1, 'amount' => 1234.56];

        $result = $this->sut->recordCashPayment(
            $fee,
            'cust_ref',
            '1234.56',
            ['day' => '07', 'month' => '01', 'year' => '2015'],
            'Payer',
            '123456'
        );

        $this->assertTrue($result);

        $this->assertCount(2, $this->logWriter->events);
        $this->assertEquals('Cash payment request', $this->logWriter->events[0]['message']);
        $this->assertEquals('Cash payment response', $this->logWriter->events[1]['message']);
    }

    /**
     * @expectedException Common\Service\Cpms\PaymentInvalidAmountException
     */
    public function testRecordCashPaymentPartPaymentThrowsException()
    {
        $fee = ['id' => 1, 'amount' => 1234.56];

        $this->sut->recordCashPayment(
            $fee,
            'cust_ref',
            '234.56', // not enough!
            ['day' => '07', 'month' => '01', 'year' => '2015'],
            'Payer',
            '123456'
        );
    }

    public function testRecordCashPaymentFailureReturnsFalse()
    {
        $this->client->shouldReceive('post')
            ->with('/api/payment/cash', 'CASH', m::any())
            ->andReturn(
                [   // error responses aren't well documented
                    'code' => 'xxx',
                    'message' => 'error message',
                ]
            )
            ->getMock();

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
            ->shouldReceive('forceUpdate')
            ->never()
            ->getMock()
        );
        $this->sm->setService(
            'Listener\Fee',
            m::mock()
            ->shouldReceive('trigger')
            ->never()
            ->getMock()
        );

        $this->sut->setServiceLocator($this->sm);

        $fee = ['id' => 1, 'amount' => 1234.56];

        $result = $this->sut->recordCashPayment(
            $fee,
            'cust_ref',
            '1234.56',
            ['day' => '07', 'month' => '01', 'year' => '2015'],
            'Payer',
            '123456'
        );

        $this->assertFalse($result);
    }

    public function testRecordChequePayment()
    {
        $params = [
            'customer_reference' => 'cust_ref',
            'scope' => 'CHEQUE',
            'total_amount' => (double)1234.56,
            'payment_data' => [
                [
                    'amount' => (double)1234.56,
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payer_details' => 'Payer',
                    'payment_reference' => [
                        'slip_number' => '123456',
                        'receipt_date' => '08-01-2015',
                        'cheque_number' => '234567',
                        'rule_start_date' => null, // tested separately
                    ],
                ]
            ],
            'cost_centre' => '12345,67890',
        ];

        $this->client->shouldReceive('post')
            ->with('/api/payment/cheque', 'CHEQUE', $params)
            ->andReturn(
                [
                    'code' => '000',
                    'message' => 'Success',
                    'receipt_reference' => 'unique_reference'
                ]
            )
            ->getMock();

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
            ->shouldReceive('forceUpdate')
            ->with(
                1,
                [
                    'feeStatus'          => 'lfs_pd', //FeeEntityService::STATUS_PAID
                    'receivedDate'       => '08-01-2015',
                    'receiptNo'          => 'unique_reference',
                    'paymentMethod'      => 'fpm_cheque', //FeePaymentEntityService::METHOD_CHEQUE
                    'receivedAmount'     => '1234.56',
                    'payerName'          => 'Payer',
                    'payingInSlipNumber' => '123456',
                    'chequePoNumber'     => '234567',
                ]
            )
            ->getMock()
        );
        $this->sm->setService(
            'Listener\Fee',
            m::mock()
            ->shouldReceive('trigger')
            ->with(1, FeeListenerService::EVENT_PAY)
            ->getMock()
        );

        $this->sut->setServiceLocator($this->sm);

        $fee = ['id' => 1, 'amount' => 1234.56];

        $result = $this->sut->recordChequePayment(
            $fee,
            'cust_ref',
            '1234.56',
            ['day' => '08', 'month' => '01', 'year' => '2015'],
            'Payer',
            '123456',
            '234567'
        );

        $this->assertTrue($result);

        $this->assertCount(2, $this->logWriter->events);
        $this->assertEquals('Cheque payment request', $this->logWriter->events[0]['message']);
        $this->assertEquals('Cheque payment response', $this->logWriter->events[1]['message']);
    }

    /**
     * @expectedException Common\Service\Cpms\PaymentInvalidAmountException
     */
    public function testRecordChequePaymentPartPaymentThrowsException()
    {
        $fee = ['id' => 1, 'amount' => 1234.56];

        $this->sut->recordChequePayment(
            $fee,
            'cust_ref',
            '234.56', // not enough!
            ['day' => '08', 'month' => '01', 'year' => '2015'],
            'Payer',
            '123456',
            '234567'
        );
    }

    public function testRecordChequePaymentFailureReturnsFalse()
    {
        $this->client->shouldReceive('post')
            ->with('/api/payment/cheque', 'CHEQUE', m::any())
            ->andReturn(
                [
                    'code' => 'xxx',
                    'message' => 'error message',
                ]
            )
            ->getMock();

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
            ->shouldReceive('forceUpdate')
            ->never()
            ->getMock()
        );
        $this->sm->setService(
            'Listener\Fee',
            m::mock()
            ->shouldReceive('trigger')
            ->never()
            ->getMock()
        );

        $this->sut->setServiceLocator($this->sm);

        $fee = ['id' => 1, 'amount' => 1234.56];

        $result = $this->sut->recordChequePayment(
            $fee,
            'cust_ref',
            '1234.56',
            ['day' => '07', 'month' => '01', 'year' => '2015'],
            'Payer',
            '123456',
            '234567'
        );

        $this->assertFalse($result);
    }

    public function testRecordPostalOrderPayment()
    {
        $params = [
            'customer_reference' => 'cust_ref',
            'scope' => 'POSTAL_ORDER',
            'total_amount' => (double)1234.56,
            'payment_data' => [
                [
                    'amount' => (double)1234.56,
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payer_details' => 'Payer',
                    'payment_reference' => [
                        'slip_number' => '123456',
                        'receipt_date' => '08-01-2015',
                        'postal_order_number' => ['234567'], // array expected according to api docs
                        'rule_start_date' => null, // tested separately
                    ],
                ]
            ],
            'cost_centre' => '12345,67890',
        ];

        $this->client->shouldReceive('post')
            ->with('/api/payment/postal-order', 'POSTAL_ORDER', $params)
            ->andReturn(
                [
                    'code' => '000',
                    'message' => 'Success',
                    'receipt_reference' => 'unique_reference'
                ]
            )
            ->getMock();

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
            ->shouldReceive('forceUpdate')
            ->with(
                1,
                [
                    'feeStatus'          => 'lfs_pd', //FeeEntityService::STATUS_PAID
                    'receivedDate'       => '08-01-2015',
                    'receiptNo'          => 'unique_reference',
                    'paymentMethod'      => 'fpm_po', //FeePaymentEntityService::METHOD_POSTAL_ORDER
                    'receivedAmount'     => '1234.56',
                    'payerName'          => 'Payer',
                    'payingInSlipNumber' => '123456',
                    'chequePoNumber'     => '234567',
                ]
            )
            ->getMock()
        );
        $this->sm->setService(
            'Listener\Fee',
            m::mock()
            ->shouldReceive('trigger')
            ->with(1, FeeListenerService::EVENT_PAY)
            ->getMock()
        );

        $this->sut->setServiceLocator($this->sm);

        $fee = ['id' => 1, 'amount' => 1234.56];

        $result = $this->sut->recordPostalOrderPayment(
            $fee,
            'cust_ref',
            '1234.56',
            ['day' => '08', 'month' => '01', 'year' => '2015'],
            'Payer',
            '123456',
            '234567'
        );

        $this->assertTrue($result);

        $this->assertCount(2, $this->logWriter->events);
        $this->assertEquals('Postal order payment request', $this->logWriter->events[0]['message']);
        $this->assertEquals('Postal order payment response', $this->logWriter->events[1]['message']);
    }

    /**
     * @expectedException Common\Service\Cpms\PaymentInvalidAmountException
     */
    public function testRecordPostalOrderPaymentPartPaymentThrowsException()
    {
        $fee = ['id' => 1, 'amount' => 1234.56];

        $this->sut->recordPostalOrderPayment(
            $fee,
            'cust_ref',
            '234.56', // not enough!
            ['day' => '08', 'month' => '01', 'year' => '2015'],
            'Payer',
            '123456',
            '234567'
        );
    }

    public function testRecordPostalOrderPaymentFailureReturnsFalse()
    {
        $this->client->shouldReceive('post')
            ->with('/api/payment/postal-order', 'POSTAL_ORDER', m::any())
            ->andReturn(
                [
                    'code' => 'xxx',
                    'message' => 'error message',
                ]
            )
            ->getMock();

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
            ->shouldReceive('forceUpdate')
            ->never()
            ->getMock()
        );
        $this->sm->setService(
            'Listener\Fee',
            m::mock()
            ->shouldReceive('trigger')
            ->never()
            ->getMock()
        );

        $this->sut->setServiceLocator($this->sm);

        $fee = ['id' => 1, 'amount' => 1234.56];

        $result = $this->sut->recordPostalOrderPayment(
            $fee,
            'cust_ref',
            '1234.56',
            ['day' => '07', 'month' => '01', 'year' => '2015'],
            'Payer',
            '123456',
            '234567'
        );

        $this->assertFalse($result);
    }

    /**
     * @dataProvider ruleStartDateProvider
     */
    public function testRuleStartDateCalculation($fee, $expectedDateStr)
    {
        $this->mockDate('2015-01-20');

        $this->assertEquals($expectedDateStr, $this->sut->getRuleStartDate($fee));
    }

    public function ruleStartDateProvider()
    {
        return [
            'immediate rule' => [
                [
                    'id' => 88,
                    'amount' => '99.99',
                    'feeType' => [
                        'accrualRule' => ['id' => 'acr_immediate']
                        // Common\Service\Data\FeeTypeDataService::ACCRUAL_RULE_IMMEDIATE
                    ],
                ],
                '20-01-2015'
            ],
            'licence start date rule' => [
                [
                    'id' => 89,
                    'amount' => '99.99',
                    'feeType' => [
                        'accrualRule' => ['id' => 'acr_licence_start']
                        // Common\Service\Data\FeeTypeDataService::ACCRUAL_RULE_LICENCE_START
                    ],
                    'licence' => [
                        'id' => 7,
                        'inForceDate' => '2015-02-28',
                    ]
                ],
                '28-02-2015'
            ],
            'continuation date rule' => [
                [
                    'id' => 90,
                    'amount' => '99.99',
                    'feeType' => [
                        'accrualRule' => ['id' => 'acr_continuation']
                        // Common\Service\Data\FeeTypeDataService::ACCRUAL_RULE_CONTINUATION
                    ],
                    'licence' => [
                        'id' => 7,
                        'expiryDate' => '2015-03-31',
                    ]
                ],
                '01-04-2015'
            ],
            'no accrualRule' => [
                [],
                null,
            ],
            'licence start with no inForceDate' => [
                [
                    'id' => 91,
                    'amount' => '99.99',
                    'feeType' => [
                        'accrualRule' => ['id' => 'acr_licence_start']
                    ],
                    'licence' => []
                ],
                null
            ],
            'continuation with no expiryDate' => [
                [
                    'id' => 92,
                    'amount' => '99.99',
                    'feeType' => [
                        'accrualRule' => ['id' => 'acr_continuation']
                    ],
                    'licence' => []
                ],
                null
            ],
            'invalid accrualRule' => [
                [
                    'id' => 93,
                    'amount' => '99.99',
                    'feeType' => [
                        'accrualRule' => ['id' => 'unknown']
                    ],
                ],
                null
            ],
        ];
    }
}
