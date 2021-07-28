<?php

/**
 * Variation Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\OperatingCentres\VariationOperatingCentres;
use Common\FormService\FormServiceInterface;
use Common\FormService\FormServiceManager;
use Common\Service\Table\TableBuilder;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\Http\Request;
use Common\Service\Helper\FormHelperService;

/**
 * Variation Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentresTest extends MockeryTestCase
{
    protected $form;

    /**
     * @var VariationOperatingCentres
     */
    protected $sut;

    protected $mockFormHelper;

    protected $tableBuilder;

    protected $translator;

    public function setUp(): void
    {
        $this->tableBuilder = m::mock();

        $this->translator = m::mock();

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Table', $this->tableBuilder);
        $sm->setService('Helper\Translation', $this->translator);

        $fsm = m::mock(FormServiceManager::class)->makePartial();
        $fsm->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $this->form = m::mock(Form::class);

        $lvaVariation = m::mock(FormServiceInterface::class);
        $lvaVariation->shouldReceive('alterForm')
            ->once()
            ->with($this->form);

        $fsm->setService('lva-variation', $lvaVariation);

        $this->mockFormHelper = m::mock(FormHelperService::class);
        $this->mockFormHelper->shouldReceive('createForm')
            ->once()
            ->with('Lva\OperatingCentres')
            ->andReturn($this->form);

        $this->sut = new VariationOperatingCentres();
        $this->sut->setFormHelper($this->mockFormHelper);
        $this->sut->setFormServiceLocator($fsm);
    }

    public function testGetForm()
    {
        $params = [
            'operatingCentres' => [],
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'licence' => [
                'totAuthVehicles' => 11,
                'totAuthTrailers' => 12
            ]
        ];

        $this->mockPopulateFormTable([]);

        $this->mockFormHelper->shouldReceive('getValidator->setMessage')
            ->with('OperatingCentreNoOfOperatingCentres.required', 'required');

        $this->mockFormHelper->shouldReceive('remove')
            ->once()
            ->with($this->form, 'dataTrafficArea');

        $this->translator->shouldReceive('translateReplace')
            ->with('current-authorisation-hint', [11])
            ->andReturn('current-authorisation-hint-11')
            ->shouldReceive('translateReplace')
            ->with('current-authorisation-hint', [12])
            ->andReturn('current-authorisation-hint-12');

        $data = m::mock();
        $data->shouldReceive('has')
            ->with('totAuthVehicles')
            ->andReturn(true)
            ->shouldReceive('has')
            ->with('totAuthTrailers')
            ->andReturn(true)
            ->shouldReceive('has')
            ->with('totCommunityLicences')
            ->andReturn(true)
            ->shouldReceive('get')
            ->with('totAuthVehicles')
            ->andReturn(
                m::mock()
                ->shouldReceive('setOption')
                ->with('hint', 'current-authorisation-hint-11')
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('totAuthTrailers')
            ->andReturn(
                m::mock()
                    ->shouldReceive('setOption')
                    ->with('hint', 'current-authorisation-hint-12')
                    ->getMock()
            );

        $this->form->shouldReceive('get')
            ->with('data')
            ->andReturn($data);

        $this->mockFormHelper->shouldReceive('disableElement')
            ->with($this->form, 'data->totCommunityLicences');

        $form = $this->sut->getForm($params);
        $this->assertSame($this->form, $form);
    }

    protected function mockPopulateFormTable($data)
    {
        $rows = [
            ['noOfLgvVehiclesRequired' => 1]
        ];

        $table = m::mock(TableBuilder::class);
        $table->shouldReceive('getRows')
            ->andReturn($rows);

        $tableElement = m::mock(Fieldset::class);
        $tableElement->shouldReceive('get')
            ->with('table')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getTable')
            ->withNoArgs()
            ->andReturn($table);

        $this->form->shouldReceive('get')
            ->with('table')
            ->andReturn($tableElement);

        $this->tableBuilder->shouldReceive('prepareTable')
            ->with('lva-variation-operating-centres', $data, [])
            ->andReturn($table);

        $this->mockFormHelper->shouldReceive('populateFormTable')
            ->with($tableElement, $table);

        return $tableElement;
    }
}
