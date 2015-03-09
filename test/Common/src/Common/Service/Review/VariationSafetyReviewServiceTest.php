<?php

/**
 * Variation Safety Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Review\VariationSafetyReviewService;

/**
 * Variation Safety Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationSafetyReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationSafetyReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        $data = [];

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->with('variation-review-safety-change')
            ->andReturn('variation-review-safety-change-translated');

        $expected = [
            'freetext' => 'variation-review-safety-change-translated'
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
