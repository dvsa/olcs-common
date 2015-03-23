<?php

/**
 * Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\Licence;

/**
 * Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Licence();
    }

    public function testAlterForm()
    {
        $form = m::mock('\Zend\Form\Form');

        $form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                ->shouldReceive('remove')
                ->with('saveAndContinue')
                ->getMock()
            );

        $this->assertNull($this->sut->alterForm($form));
    }
}
