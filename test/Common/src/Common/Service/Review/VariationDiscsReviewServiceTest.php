<?php

/**
 * Variation Discs Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Review\VariationDiscsReviewService;

/**
 * Variation Discs Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationDiscsReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationDiscsReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        $data = [];

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->with('variation-review-discs-change')
            ->andReturn('variation-review-discs-change-translated');

        $expected = [
            'freetext' => 'variation-review-discs-change-translated'
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
