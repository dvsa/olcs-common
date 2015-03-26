<?php

/**
 * Variation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\Variation;

/**
 * Variation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Variation();
    }

    /**
     * No op
     */
    public function testAlterForm()
    {
        $form = m::mock('\Zend\Form\Form');

        $this->assertNull($this->sut->alterForm($form));
    }
}
