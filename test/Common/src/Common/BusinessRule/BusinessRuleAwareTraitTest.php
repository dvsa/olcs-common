<?php

/**
 * Business Rule Aware Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessRule;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Business Rule Aware Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessRuleAwareTraitTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = $this->getMockForTrait('\Common\BusinessRule\BusinessRuleAwareTrait');
    }

    public function testSetterGetter()
    {
        $businessRuleManager = m::mock('\Common\BusinessRule\BusinessRuleManager');

        $this->sut->setBusinessRuleManager($businessRuleManager);

        $this->assertSame($businessRuleManager, $this->sut->getBusinessRuleManager());
    }
}
