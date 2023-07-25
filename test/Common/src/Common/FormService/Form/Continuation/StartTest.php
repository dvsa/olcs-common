<?php

namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Continuation\Start;
use Common\Form\Model\Form\Continuation\Start as StartForm;
use Common\Service\Helper\FormHelperService;

/**
 * Licence checklist form service test
 */
class StartTest extends MockeryTestCase
{
    /** @var StartForm */
    protected $sut;
    /** @var  m\MockInterface */
    private $formHelper;

    public function setUp(): void
    {
        $this->formHelper = m::mock(FormHelperService::class);

        $this->sut = new Start($this->formHelper);
    }

    public function testGetForm()
    {
        $form = m::mock(StartForm::class);

        $this->formHelper
            ->shouldReceive('createForm')
            ->with(StartForm::class)
            ->andReturn($form)
            ->once()
            ->getMock();

        $this->assertEquals($form, $this->sut->getForm());
    }
}
