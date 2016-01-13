<?php

/**
 * Fee Payment Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\FeePaymentEntityService;

/**
 * Fee Payment Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeePaymentEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new FeePaymentEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     * @dataProvider paymentTypeProvider
     */
    public function testIsValidPaymentType($expected, $type)
    {
        $this->assertEquals($expected, $this->sut->isValidPaymentType($type));
    }

    public function paymentTypeProvider()
    {
        return [
            [true, 'fpm_cash'],
            [true, 'fpm_cheque'],
            [true, 'fpm_po'],
            [true, 'fpm_card_offline'],
            [true, 'fpm_card_online'],
            [true, 'fpm_waive'],
            [false, 'invalid'],
            [false, ''],
            [false, null],
        ];
    }


    /**
     * @group entity_services
     */
    public function testGetFeesByPaymentId()
    {
        $id = 22;

        $query = ['payment' => $id];

        $response = array(
            'Count' => 2,
            'Results' => [
                [
                    'fee' => [ 'id' => 77, 'amount' => 0.99],
                ],
                [
                    'fee' => [ 'id' => 78, 'amount' => 1.99],
                ],
            ],
        );

        $this->expectOneRestCall('FeePayment', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals(
            [
                [ 'id' => 77, 'amount' => 0.99],
                [ 'id' => 78, 'amount' => 1.99],
            ],
            $this->sut->getFeesByPaymentId($id)
        );
    }
}
