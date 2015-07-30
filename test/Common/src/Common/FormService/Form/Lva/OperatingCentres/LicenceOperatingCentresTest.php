<?php

/**
 * Licence Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\OperatingCentres\LicenceOperatingCentres;
use Common\FormService\FormServiceInterface;
use Common\FormService\FormServiceManager;
use Common\Service\Table\TableBuilder;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Http\Request;
use Common\Service\Helper\FormHelperService;

/**
 * Licence Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentresTest extends MockeryTestCase
{
    protected $form;

    /**
     * @var LicenceOperatingCentres
     */
    protected $sut;

    protected $mockFormHelper;

    protected $tableBuilder;

    public function setUp()
    {
        $this->tableBuilder = m::mock();

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Table', $this->tableBuilder);

        $fsm = m::mock(FormServiceManager::class)->makePartial();
        $fsm->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $this->form = m::mock(Form::class);

        $lvaLicence = m::mock(FormServiceInterface::class);
        $lvaLicence->shouldReceive('alterForm')
            ->once()
            ->with($this->form);

        $fsm->setService('lva-licence', $lvaLicence);

        $this->mockFormHelper = m::mock(FormHelperService::class);
        $this->mockFormHelper->shouldReceive('createForm')
            ->once()
            ->with('Lva\OperatingCentres')
            ->andReturn($this->form);

        $this->sut = new LicenceOperatingCentres();
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
        ];

        $this->mockPopulateFormTable([]);

        $fields = [
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        ];

        $this->mockFormHelper->shouldReceive('removeFieldList')
            ->once()
            ->with($this->form, 'data', $fields);

        $this->mockFormHelper->shouldReceive('getValidator->setMessage')
            ->with('OperatingCentreNoOfOperatingCentres.required', 'required');

        $this->mockFormHelper->shouldReceive('remove')
            ->once()
            ->with($this->form, 'dataTrafficArea');

        $form = $this->sut->getForm($params);
        $this->assertSame($this->form, $form);
    }

    public function testGetFormWithoutS41OrCommunityLicences()
    {
        $params = [
            'operatingCentres' => [],
            'canHaveSchedule41' => false,
            'canHaveCommunityLicences' => false,
            'isPsv' => false,
        ];

        $tableElement = $this->mockPopulateFormTable([]);

        $fields = [
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        ];

        $this->mockFormHelper->shouldReceive('removeFieldList')
            ->once()
            ->with($this->form, 'data', $fields);

        $this->mockFormHelper->shouldReceive('getValidator->setMessage')
            ->with('OperatingCentreNoOfOperatingCentres.required', 'required');

        $this->mockFormHelper->shouldReceive('remove')
            ->once()
            ->with($this->form, 'dataTrafficArea');

        $tableElement->shouldReceive('get->getTable->removeAction')
            ->once()
            ->with('schedule41');

        $this->mockFormHelper->shouldReceive('remove')
            ->once()
            ->with($this->form, 'data->totCommunityLicences');

        $form = $this->sut->getForm($params);
        $this->assertSame($this->form, $form);
    }

    public function testGetFormPsv()
    {
        $params = [
            'operatingCentres' => [],
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => true,
            'canHaveLargeVehicles' => false
        ];

        $tableElement = $this->mockPopulateFormTable([]);

        $footer = [
            'total' => [
                'content' => 'foo'
            ],
            'trailersCol' => 'foo'
        ];

        $table = m::mock();
        $table->shouldReceive('removeColumn')
            ->once()
            ->with('noOfTrailersRequired')
            ->shouldReceive('getFooter')
            ->andReturn($footer)
            ->shouldReceive('setFooter')
            ->with(['total' => ['content' => 'foo-psv']]);

        $tableElement->shouldReceive('get->getTable')
            ->andReturn($table);

        $this->mockFormHelper->shouldReceive('getValidator->setMessage')
            ->with('OperatingCentreNoOfOperatingCentres.required', 'required');

        $this->mockFormHelper->shouldReceive('remove')
            ->once()
            ->with($this->form, 'dataTrafficArea');

        $data = m::mock();
        $data->shouldReceive('getOptions')
            ->andReturn(['hint' => 'foo'])
            ->shouldReceive('setOptions')
            ->with(['hint' => 'foo.psv']);

        $this->form->shouldReceive('get')
            ->with('data')
            ->andReturn($data);

        $fields = [
            'totAuthTrailers',
            'totAuthLargeVehicles'
        ];

        $this->mockFormHelper->shouldReceive('removeFieldList')
            ->once()
            ->with($this->form, 'data', $fields);

        $form = $this->sut->getForm($params);
        $this->assertSame($this->form, $form);
    }

    public function testGetFormWithOcs()
    {
        $params = [
            'operatingCentres' => ['foo' => 'bar'],
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'licence' => [
                'trafficArea' => [
                    'id' => 123,
                    'name' => 'TA'
                ]
            ],
            'possibleEnforcementAreas' => [
                'a', 'b', 'c'
            ]
        ];

        $this->mockPopulateFormTable(['foo' => 'bar']);

        $fields = [
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        ];

        $this->mockFormHelper->shouldReceive('removeFieldList')
            ->once()
            ->with($this->form, 'data', $fields);

        $this->mockFormHelper->shouldReceive('getValidator->setMessage')
            ->with('OperatingCentreNoOfOperatingCentres.required', 'required');

        $this->mockFormHelper->shouldReceive('remove')
            ->once()
            ->with($this->form, 'dataTrafficArea->trafficArea');

        $trafficArea = m::mock();
        $trafficArea->shouldReceive('get')
            ->with('enforcementArea')
            ->andReturn(
                m::mock()
                ->shouldReceive('setValueOptions')
                ->with(['a', 'b', 'c'])
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('trafficAreaSet')
            ->andReturn(
                m::mock()
                ->shouldReceive('setValue')
                ->with('TA')
                ->andReturnSelf()
                ->shouldReceive('setOption')
                ->with('hint-suffix', '-operating-centres')
                ->getMock()
            );

        $this->form->shouldReceive('get')
            ->with('dataTrafficArea')
            ->andReturn($trafficArea);

        $form = $this->sut->getForm($params);
        $this->assertSame($this->form, $form);
    }

    public function testGetFormWithOcsNoTa()
    {
        $params = [
            'operatingCentres' => ['foo' => 'bar'],
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'licence' => [
                'trafficArea' => []
            ],
            'possibleTrafficAreas' => ['a', 'b']
        ];

        $this->mockPopulateFormTable(['foo' => 'bar']);

        $fields = [
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        ];

        $this->mockFormHelper->shouldReceive('removeFieldList')
            ->once()
            ->with($this->form, 'data', $fields);

        $this->mockFormHelper->shouldReceive('getValidator->setMessage')
            ->with('OperatingCentreNoOfOperatingCentres.required', 'required');

        $trafficArea = m::mock();
        $trafficArea->shouldReceive('remove')
            ->with('trafficAreaSet')
            ->andReturnSelf()
            ->shouldReceive('remove')
            ->with('enforcementArea')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('trafficArea')
            ->andReturn(
                m::mock()
                ->shouldReceive('setValueOptions')
                ->with(['a', 'b'])
                ->getMock()
            );

        $this->form->shouldReceive('get')
            ->with('dataTrafficArea')
            ->andReturn($trafficArea);

        $form = $this->sut->getForm($params);
        $this->assertSame($this->form, $form);
    }

    protected function mockPopulateFormTable($data)
    {
        $table = m::mock(TableBuilder::class);
        $tableElement = m::mock(Fieldset::class);

        $this->form->shouldReceive('get')
            ->with('table')
            ->andReturn($tableElement);

        $this->tableBuilder->shouldReceive('prepareTable')
            ->with('lva-operating-centres', $data)
            ->andReturn($table);

        $this->mockFormHelper->shouldReceive('populateFormTable')
            ->with($tableElement, $table);

        return $tableElement;
    }
}
