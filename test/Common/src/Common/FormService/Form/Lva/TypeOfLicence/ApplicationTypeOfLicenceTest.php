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

    /**
     * Test set and lock operator location
     *
     * @dataProvider lockOperatorLocationProvider
     * @param string $message
     * @param string $locationValue
     * @param string $location
     */
    public function testSetAndLockOperatorLocation($message, $location, $locationValue)
    {
        $mockOperatorLocation = m::mock(\Zend\Form\Element::class)
            ->shouldReceive('setValue')
            ->with($locationValue)
            ->once()
            ->getMock();


        $mockForm = m::mock(Form::class)
            ->shouldReceive('get')
            ->with('type-of-licence')
            ->once()
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('operator-location')
                ->twice()
                ->andReturn($mockOperatorLocation)
                ->getMock()
            )
            ->getMock();

        $this->fh->shouldReceive('disableElement')
            ->with($mockForm, 'type-of-licence->operator-location')
            ->once()
            ->shouldReceive('lockElement')
            ->with($mockOperatorLocation, $message)
            ->once()
            ->getMock();

        $this->sut->setAndLockOperatorLocation($mockForm, $location);
    }

    /**
     * Lock operator location provider
     */
    public function lockOperatorLocationProvider()
    {
        return [
            ['alternative-operator-location-lock-message-ni', 'NI', 'Y'],
            ['alternative-operator-location-lock-message-gb', 'GB', 'N']
        ];
    }
}
