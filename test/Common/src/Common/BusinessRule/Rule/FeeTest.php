<?php

/**
 * Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\BusinessRule\Rule;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessRule\Rule\Fee;
use Common\Service\Entity\FeeEntityService;

/**
 * Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new Fee();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testValidate()
    {
        $mockDateHelper = m::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);
        $mockDateHelper->shouldReceive('getDate')
            ->andReturn('2015-01-01');

        $data = [
            'fee-details' => [
                'amount' => '99.99',
                'createdDate' => '2015-04-15',
                'feeType' => 'TYPE',
            ],
            'user' => 2,
        ];
        $description = 'test description';

        $expected = [
            'amount' => '99.99',
            'invoicedDate' => '2015-04-15',
            'feeType' => 'TYPE',
            'description' => 'test description',
            'feeStatus' => FeeEntityService::STATUS_OUTSTANDING,
            'createdBy' => 2,
            'lastModifiedBy' => 2,
            'createdOn' => '2015-01-01',
            'lastModifiedOn' => '2015-01-01',
        ];

        $this->assertEquals($expected, $this->sut->validate($data, $description));
    }
}
