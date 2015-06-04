<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\FormService\Form\Lva\Application;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Form;

/**
 * Application Type Of Licence Test
 */
class ApplicationTypeOfLicenceTest extends MockeryTestCase
{
    /**
     * @var ApplicationTypeOfLicence
     */
    protected $sut;

    protected $fsm;

    protected $fh;

    public function setUp()
    {
        $this->sut = new ApplicationTypeOfLicence();

        $this->fsm = m::mock(FormServiceManager::class)->makePartial();
        $this->fh = m::mock(FormHelperService::class)->makePartial();

        $this->sut->setFormServiceLocator($this->fsm);
        $this->sut->setFormHelper($this->fh);
    }

    public function testGetForm()
    {
        $mockForm = m::mock(Form::class);

        $this->fh->shouldReceive('createForm')
            ->once()
            ->with('Lva\TypeOfLicence')
            ->andReturn($mockForm);

        $appService = m::mock(Application::class);
        $this->fsm->setService('lva-application', $appService);

        $appService->shouldReceive('alterForm')
            ->once()
            ->with($mockForm);

        $form = $this->sut->getForm([]);

        $this->assertSame($mockForm, $form);
    }
}
