<?php

/**
 * Variation Business Details Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Review\VariationBusinessDetailsReviewService;

/**
 * Variation Business Details Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationBusinessDetailsReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationBusinessDetailsReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        $data = [];

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->with('variation-review-business-details-change')
            ->andReturn('variation-review-business-details-change-translated');

        $expected = [
            'freetext' => 'variation-review-business-details-change-translated'
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
