<?php

/**
 * Variation Financial Evidence Adapter Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace OlcsTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\VariationFinancialEvidenceAdapter;

/**
 * Variation Financial Evidence Adapter Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class VariationFinancialEvidenceAdapterTest extends MockeryTestCase
{
    public function testAlterFormForLva()
    {
        $sut = new VariationFinancialEvidenceAdapter();

        $mockElement = m::mock()
            ->shouldReceive('setValue')
            ->once()
            ->with('markup-required-finance-variation')
            ->getMock();

        $mockFieldset = m::mock()
            ->shouldReceive('get')
            ->once()
            ->with('requiredFinance')
            ->andReturn($mockElement)
            ->getMock();

        $mockForm = m::mock()
            ->shouldReceive('get')
            ->once()
            ->with('finance')
            ->andReturn($mockFieldset)
            ->getMock();

        $sut->alterFormForLva($mockForm);
    }
}
