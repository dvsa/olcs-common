<?php

/**
 * Abstract Form Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract Form Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractFormServiceTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = m::mock('\Common\FormService\Form\AbstractFormService')->makePartial();
    }

    public function testSetterGetter()
    {
        $fsl = m::mock('\Common\FormService\FormServiceManager');

        $this->sut->setFormServiceLocator($fsl);

        $this->assertSame($fsl, $this->sut->getFormServiceLocator());
    }
}
