<?php

/**
 * Variation LVA Service tests
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Printing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Lva\VariationLvaService;

/**
 * Variation LVA Service tests
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationLvaServiceTest extends MockeryTestCase
{
    private $sm;
    private $sut;

    public function setup()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new VariationLvaService();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testAddVariationMessage()
    {
        $licenceId = 123;

        $mockUrl = m::mock();
        $mockTranslator = m::mock();
        $mockGuidance = m::mock();

        $this->sm->setService('Helper\Url', $mockUrl);
        $this->sm->setService('Helper\Translation', $mockTranslator);
        $this->sm->setService('Helper\Guidance', $mockGuidance);

        $mockUrl->shouldReceive('fromRoute')
            ->with('lva-licence/variation', ['licence' => 123])
            ->andReturn('URL');

        $mockTranslator->shouldReceive('translateReplace')
            ->with('variation-message', ['URL'])
            ->andReturn('translated-message');

        $mockGuidance->shouldReceive('append')
            ->with('translated-message');

        $this->sut->addVariationMessage($licenceId);
    }
}
