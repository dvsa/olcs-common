<?php

/**
 * Variation Type Of Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Review\VariationTypeOfLicenceReviewService;

/**
 * Variation Type Of Licence Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTypeOfLicenceReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationTypeOfLicenceReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        $data = [
            'licenceType' => [
                'description' => 'foo'
            ],
            'licence' => [
                'licenceType' => [
                    'description' => 'bar'
                ]
            ]
        ];
        $expectedTranslationParams = [
            'bar',
            'foo'
        ];

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translateReplace')
            ->with('variation-application-type-of-licence-freetext', $expectedTranslationParams)
            ->andReturn('translated-text');

        $this->assertEquals(['freetext' => 'translated-text'], $this->sut->getConfigFromData($data));
    }
}
