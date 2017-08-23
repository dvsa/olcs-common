<?php

namespace CommonTest\FormService\Form\Lva;

use Common\FormService\Form\Lva\FinancialEvidence;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\FormService\Form\Lva\FinancialEvidence
 */
class FinancialEvidenceTest extends MockeryTestCase
{
    /** @var  FinancialEvidence */
    protected $sut;

    /** @var  m\MockInterface|\Common\Service\Helper\FormHelperService */
    protected $formHelper;
    /** @var  \Common\FormService\FormServiceManager */
    protected $fsm;

    public function setUp()
    {
        $this->formHelper = m::mock(\Common\Service\Helper\FormHelperService::class);
        $this->fsm = m::mock(\Common\FormService\FormServiceManager::class)->makePartial();

        $this->sut = new FinancialEvidence();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->fsm);
    }

    public function testGetForm()
    {
        $mockUrlService = m::mock()
            ->shouldReceive('fromRoute')
            ->with('guides/guide', ['guide' => 'financial-evidence'], [], true)
            ->andReturn('FOO')
            ->once()
            ->getMock();

        $mockTranslatorService = m::mock()
            ->shouldReceive('translateReplace')
            ->with('lva-financial-evidence-evidence.hint', ['FOO'])
            ->andReturn('BAR')
            ->once()
            ->getMock();

        $mockServiceLocator =  m::mock()
            ->shouldReceive('get')
            ->with('ControllerPluginManager')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('url')
                    ->andReturn($mockUrlService)
                    ->getMock()
            )
            ->once()
            ->shouldReceive('get')
            ->with('Helper\Translation')
            ->andReturn($mockTranslatorService)
            ->once()
            ->getMock();

        $this->fsm
            ->shouldReceive('getServiceLocator')
            ->andReturn($mockServiceLocator)
            ->once()
            ->getMock();

        /** @var \Zend\Http\Request $request */
        $request = m::mock(\Zend\Http\Request::class);

        // Mocks
        $mockForm = m::mock()
            ->shouldReceive('get')
            ->with('evidence')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('uploadNowRadio')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('setName')
                            ->with('uploadNow')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('get')
                    ->with('uploadLaterRadio')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('setName')
                            ->with('uploadNow')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('get')
                    ->with('sendByPostRadio')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('setName')
                            ->with('uploadNow')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('setOption')
                    ->with('hint', 'BAR')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\FinancialEvidence', $request)
            ->andReturn($mockForm)
            ->shouldReceive('remove')
            ->with($mockForm, 'evidence->uploadNow')
            ->once()
            ->getMock();

        $form = $this->sut->getForm($request);

        $this->assertSame($mockForm, $form);
    }
}
