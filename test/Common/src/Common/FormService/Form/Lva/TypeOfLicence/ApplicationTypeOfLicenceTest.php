<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\FormService\Form\Lva\Application;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Form;

class ApplicationTypeOfLicenceTest extends MockeryTestCase
{
    /** @var ApplicationTypeOfLicence */
    protected $sut;

    /** @var  m\MockInterface|FormServiceManager */
    protected $fsm;
    /** @var  m\MockInterface|FormHelperService */
    protected $fh;

    public function setUp(): void
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
                ->once()
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

    public function testMaybeAlterFormForNi()
    {
        $mockOperatorLocation = m::mock(\Zend\Form\Element::class)
            ->shouldReceive('getValue')
            ->andReturn('Y')
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
                    ->once()
                    ->andReturn($mockOperatorLocation)
                    ->getMock()
            )
            ->shouldReceive('getInputFilter')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('type-of-licence')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('get')
                    ->with('operator-type')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('setRequired')
                        ->with(false)
                        ->once()
                        ->getMock()
                    )
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->sut->maybeAlterFormForNi($mockForm);
    }
}
