<?php

/**
 * Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\BusinessService\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Fee;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $brm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        $this->sut = new Fee();

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessRuleManager($this->brm);
    }

    public function testProcess()
    {
        // Data
        $feeTypeId = 20051;
        $description = 'DESCRIPTION';
        $params = [
            'fee-details' => [
                'feeType' => $feeTypeId,
                'foo' => 'bar',
            ],
        ];
        $expectedSaveData = [
            'DATA_TO_SAVE',
        ];

        // Mocks
        $feeRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $this->brm->setService('Fee', $feeRule);
        $mockFeeEntity = m::mock();
        $this->sm->setService('Entity\Fee', $mockFeeEntity);
        $mockFeeTypeEntity = m::mock();
        $this->sm->setService('Entity\FeeType', $mockFeeTypeEntity);

        // Expectations
        $mockFeeTypeEntity
            ->shouldReceive('getById')
            ->with($feeTypeId)
            ->andReturn(
                [
                    'id' => $feeTypeId,
                    'description' => $description,
                ]
            );

        $feeRule->shouldReceive('validate')
            ->with($params, $description)
            ->andReturn($expectedSaveData);

        $mockFeeEntity->shouldReceive('save')
            ->with($expectedSaveData)
            ->andReturn(['id' => 123]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 123], $response->getData());
    }
}
