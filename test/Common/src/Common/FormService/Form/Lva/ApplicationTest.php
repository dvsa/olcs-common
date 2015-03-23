<?php

/**
 * Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\Application;

/**
 * Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Application();
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
