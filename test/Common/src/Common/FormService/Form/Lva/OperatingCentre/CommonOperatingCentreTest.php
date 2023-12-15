<?php

namespace CommonTest\Common\FormService\Form\Lva\OperatingCentre;

use Common\FormService\Form\Lva\OperatingCentre\CommonOperatingCentre;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Http\Request;
use Common\Service\Helper\FormHelperService;

/**
 * Common Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommonOperatingCentreTest extends MockeryTestCase
{
    protected $form;

    protected $request;

    /**
     * @var CommonOperatingCentre
     */
    protected $sut;

    protected $mockFormHelper;

    public function setUp(): void
    {
        $this->form = m::mock(Form::class);

        $this->request = m::mock(Request::class);

        $this->mockFormHelper = m::mock(FormHelperService::class);
        $this->mockFormHelper->shouldReceive('createFormWithRequest')
            ->once()
            ->with('Lva\OperatingCentre', $this->request)
            ->andReturn($this->form);

        $this->sut = new CommonOperatingCentre($this->mockFormHelper);
    }

    public function testGetForm()
    {
        $params = [
            'action' => 'edit',
            'isPsv' => false,
            'canAddAnother' => true,
            'canUpdateAddress' => true,
            'wouldIncreaseRequireAdditionalAdvertisement' => false
        ];

        $this->mockFormHelper->shouldReceive('remove')
            ->never();

        $this->form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                ->shouldReceive('remove')
                ->once()
                ->with('addAnother')
                ->getMock()
            );

        $this->form->shouldReceive('get')
            ->with('address')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('postcode')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setOption')
                    ->with('shouldEscapeMessages', false)
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->form->shouldReceive('getInputFilter->get->get->setRequired')
            ->with(false);

        $form = $this->sut->getForm($params, $this->request);
        $this->assertSame($this->form, $form);
    }

    public function testGetFormPsv()
    {
        $params = [
            'action' => 'edit',
            'isPsv' => true,
            'canAddAnother' => true,
            'canUpdateAddress' => true,
            'wouldIncreaseRequireAdditionalAdvertisement' => false
        ];

        $permission = m::mock();

        $dataFieldset = m::mock();
        $dataFieldset
            ->shouldReceive('get')
            ->with('permission')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('permission')
                    ->andReturn($permission)
                    ->once()
                    ->getMock()
            );

        $this->form->shouldReceive('get')
            ->with('data')
            ->andReturn($dataFieldset);

        $this->mockFormHelper->shouldReceive('remove')
            ->once()
            ->with($this->form, 'data->noOfTrailersRequired')
            ->shouldReceive('remove')
            ->once()
            ->with($this->form, 'advertisements')
            ->shouldReceive('alterElementLabel')
            ->once()
            ->shouldReceive('remove')
            ->with($this->form, 'data->guidance')
            ->once()
            ->shouldReceive('remove')
            ->with($dataFieldset, '-psv', FormHelperService::ALTER_LABEL_APPEND)
            ->shouldReceive('alterElementLabel')
            ->once()
            ->with($permission, '-psv', FormHelperService::ALTER_LABEL_APPEND);

        $this->form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                    ->shouldReceive('remove')
                    ->once()
                    ->with('addAnother')
                    ->getMock()
            );

        $this->form->shouldReceive('getInputFilter->get->get->setRequired')
            ->with(false);

        $this->form->shouldReceive('get')
            ->with('address')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('postcode')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('setOption')
                            ->with('shouldEscapeMessages', false)
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $form = $this->sut->getForm($params, $this->request);
        $this->assertSame($this->form, $form);
    }

    public function testGetFormAdd()
    {
        $params = [
            'action' => 'add',
            'isPsv' => false,
            'canAddAnother' => false,
            'canUpdateAddress' => true,
            'wouldIncreaseRequireAdditionalAdvertisement' => false
        ];

        $this->mockFormHelper->shouldReceive('remove')
            ->never();

        $this->form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                    ->shouldReceive('has')
                    ->once()
                    ->with('addAnother')
                    ->andReturn(true)
                    ->shouldReceive('remove')
                    ->once()
                    ->with('addAnother')
                    ->getMock()
            );

        $this->form->shouldReceive('getInputFilter->get->get->setRequired')
            ->with(false);

        $this->form->shouldReceive('get')
            ->with('address')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('postcode')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('setOption')
                            ->with('shouldEscapeMessages', false)
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $form = $this->sut->getForm($params, $this->request);
        $this->assertSame($this->form, $form);
    }

    public function testGetFormCantUpdateAddress()
    {
        $params = [
            'action' => 'edit',
            'isPsv' => false,
            'canAddAnother' => true,
            'canUpdateAddress' => false,
            'wouldIncreaseRequireAdditionalAdvertisement' => false
        ];

        $this->mockFormHelper->shouldReceive('remove')
            ->never();

        $this->form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                    ->shouldReceive('remove')
                    ->once()
                    ->with('addAnother')
                    ->getMock()
            );

        $addressFilter = m::mock();
        $elem = m::mock(Element::class)
            ->shouldReceive('setOption')
            ->with('shouldEscapeMessages', false)
            ->once()
            ->getMock();

        $addressElement = m::mock();
        $addressElement->shouldReceive('remove')
            ->once()
            ->with('searchPostcode')
            ->shouldReceive('get')
            ->andReturn($elem);

        $this->form->shouldReceive('get')
            ->with('address')
            ->andReturn($addressElement);

        $this->form->shouldReceive('getInputFilter->get')
            ->with('address')
            ->andReturn($addressFilter);

        $this->mockFormHelper->shouldReceive('disableElements')
            ->once()
            ->with($addressElement)
            ->shouldReceive('disableValidation')
            ->once()
            ->with($addressFilter)
            ->shouldReceive('lockElement')
            ->times(4)
            ->with($elem, 'operating-centre-address-requires-variation');

        $addressFilter->shouldReceive('get->setRequired')
            ->with(false);

        $form = $this->sut->getForm($params, $this->request);
        $this->assertSame($this->form, $form);
    }

    public function testGetFormVariation()
    {
        $params = [
            'action' => 'edit',
            'isPsv' => false,
            'canAddAnother' => true,
            'canUpdateAddress' => true,
            'wouldIncreaseRequireAdditionalAdvertisement' => true,
            'currentVehiclesRequired' => 10,
            'currentTrailersRequired' => 11
        ];

        $this->mockFormHelper->shouldReceive('remove')
            ->never();

        $this->form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                    ->shouldReceive('remove')
                    ->once()
                    ->with('addAnother')
                    ->getMock()
            );

        $this->form->shouldReceive('getInputFilter->get->get->setRequired')
            ->with(false);

        $data = m::mock();
        $data->shouldReceive('has')
            ->with('noOfTrailersRequired')
            ->andReturn(true);

        $data->shouldReceive('get')
            ->with('noOfVehiclesRequired')
            ->andReturn(
                m::mock()
                ->shouldReceive('setAttribute')
                ->with('data-current', 10)
                ->getMock()
            );

        $data->shouldReceive('get')
            ->with('noOfTrailersRequired')
            ->andReturn(
                m::mock()
                    ->shouldReceive('setAttribute')
                    ->with('data-current', 11)
                    ->getMock()
            );

        $this->form->shouldReceive('get')
            ->with('data')
            ->andReturn($data);

        $this->form->shouldReceive('get')
            ->with('address')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('postcode')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('setOption')
                            ->with('shouldEscapeMessages', false)
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $form = $this->sut->getForm($params, $this->request);
        $this->assertSame($this->form, $form);
    }
}
