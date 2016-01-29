<?php

/**
 * Business Service Aware Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Business Service Aware Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessServiceAwareTraitTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = $this->getMockForTrait('\Common\BusinessService\BusinessServiceAwareTrait');
    }

    public function testSetterGetter()
    {
        $businessServiceManager = m::mock('\Common\BusinessService\BusinessServiceManager');

        $this->sut->setBusinessServiceManager($businessServiceManager);

        $this->assertSame($businessServiceManager, $this->sut->getBusinessServiceManager());
    }
}
