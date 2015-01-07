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
}
