<?php

/**
 * Form Helper Aware Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Form Helper Aware Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormHelperAwareTraitTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = $this->getMockForTrait('\Common\FormService\FormHelperAwareTrait');
    }

    public function testSetterGetter()
    {
        $formHelper = m::mock('\Common\Service\Helper\FormHelperService');

        $this->sut->setFormHelper($formHelper);

        $this->assertSame($formHelper, $this->sut->getFormHelper());
    }
}
