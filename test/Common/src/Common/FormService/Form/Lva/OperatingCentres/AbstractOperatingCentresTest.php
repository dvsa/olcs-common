<?php

namespace CommonTest\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\OperatingCentres\AbstractOperatingCentres;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;

/**
 * @covers \Common\FormService\Form\Lva\OperatingCentres\AbstractOperatingCentres
 */
class AbstractOperatingCentresTest extends MockeryTestCase
{
    /** @var  AbstractOperatingCentres */
    private $sut;

    public function setUp(): void
    {
        $this->sut = m::mock(AbstractOperatingCentres::class);
    }

    public function testAlterFormWithTrafficArea()
    {
        $params = [
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'operatingCentres' => ['XX'],
            'trafficArea' => ['id' => 'A', 'name' => 'THE NORTH'],
            'niFlag' => 'N',
            'possibleEnforcementAreas' => ['A', 'B'],
        ];

        $mockFieldSet = m::mock();
        $mockFieldSet->shouldReceive('get')->with('trafficAreaSet')->once()->andReturn(
            m::mock()
                ->shouldReceive('setValue')
                ->with('THE NORTH')
                ->once()
                ->getMock()
        );
        $mockFieldSet->shouldReceive('get')->with('enforcementArea')->once()->andReturn(
            m::mock()->shouldReceive('setValueOptions')->with(['A', 'B'])->once()->getMock()
        );

        $mockForm = m::mock(\Common\Form\Form::class);
        $mockForm->shouldReceive('get')->with('dataTrafficArea')->once()->andReturn($mockFieldSet);

        $mockFormHelper = m::mock();
        $mockFormHelper->shouldReceive('getValidator->setMessage');
        $mockFormHelper->shouldReceive('remove')->with($mockForm, 'dataTrafficArea->trafficArea')->once();

        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $this->sut->alterForm($mockForm, $params);
    }

    public function testAlterFormWithTrafficAreaNi()
    {
        $params = [
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'operatingCentres' => ['XX'],
            'trafficArea' => ['id' => 'A', 'name' => 'THE NORTH'],
            'niFlag' => 'Y',
            'possibleEnforcementAreas' => ['A', 'B']
        ];

        $mockFieldSet = m::mock();
        $mockFieldSet->shouldReceive('get')->with('trafficAreaSet')->times(2)->andReturn(
            m::mock()
                ->shouldReceive('setValue')
                ->with('THE NORTH')
                ->once()
                ->shouldReceive('setOption')
                ->with('hint', null)
                ->once()
                ->getMock()
        );
        $mockFieldSet->shouldReceive('get')->with('enforcementArea')->once()->andReturn(
            m::mock()->shouldReceive('setValueOptions')->with(['A', 'B'])->once()->getMock()
        );

        $mockForm = m::mock(\Common\Form\Form::class);
        $mockForm->shouldReceive('get')->with('dataTrafficArea')->twice()->andReturn($mockFieldSet);

        $mockFormHelper = m::mock();
        $mockFormHelper->shouldReceive('remove')->with($mockForm, 'dataTrafficArea->trafficArea')->once();
        $mockFormHelper->shouldReceive('getValidator->setMessage');
        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $this->sut->alterForm($mockForm, $params);
    }

    public function testAlterFormWithOutTrafficArea()
    {
        $mockForm = m::mock(\Common\Form\Form::class);

        $params = [
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'operatingCentres' => ['XX'],
            'trafficArea' => null,
            'niFlag' => 'N',
            'possibleTrafficAreas' => ['A', 'B'],
            'possibleEnforcementAreas' => ['A', 'B']
        ];

        $mockFormHelper = m::mock();
        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $mockFormHelper->shouldReceive('getValidator->setMessage');

        $mockFieldSet = m::mock();
        $mockForm->shouldReceive('get')->with('dataTrafficArea')->once()->andReturn($mockFieldSet);
        $mockFieldSet->shouldReceive('remove')->with('trafficAreaSet')->once();

        $mockFieldSet->shouldReceive('get')->with('trafficArea')->once()->andReturn(
            m::mock()->shouldReceive('setValueOptions')->with(['A', 'B'])->once()->getMock()
        );
        $mockFieldSet->shouldReceive('get')->with('enforcementArea')->once()->andReturn(
            m::mock()->shouldReceive('setValueOptions')->with(['A', 'B'])->once()->getMock()
        );

        $this->sut->alterForm($mockForm, $params);
    }

    public function testAlterFormForPsvLicences()
    {
        $dataOptions = ['hint' => 'foo'];
        $dataOptionsModified = ['hint' => 'foo.psv'];

        $dataFieldset = m::mock(Fieldset::class);
        $dataFieldset->shouldReceive('getOptions')
            ->andReturn($dataOptions);
        $dataFieldset->shouldReceive('setOptions')
            ->with($dataOptionsModified)
            ->once();
        $dataFieldset->shouldReceive('has')
            ->with('totCommunityLicences')
            ->andReturn(false);

        $mockForm = m::mock(\Common\Form\Form::class);
        $mockForm->shouldReceive('get')
            ->with('data')
            ->andReturn($dataFieldset);

        $mockFormHelper = m::mock()
            ->shouldReceive('removeFieldList')
            ->with($mockForm, 'data', ['totAuthTrailers'])
            ->once()
            ->getMock();

        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $this->sut->alterFormForPsvLicences($mockForm, []);
    }

    public function testAlterFormForPsvLicencesWithCommunityLicenceField()
    {
        $dataOptions = ['hint' => 'foo'];
        $dataOptionsModified = ['hint' => 'foo.psv'];

        $totCommunityLicencesElement = m::mock(Text::class);
        $totCommunityLicencesElement->shouldReceive('getLabel')
            ->andReturn('label.value');
        $totCommunityLicencesElement->shouldReceive('setLabel')
            ->with('label.value.psv')
            ->once();

        $dataFieldset = m::mock(Fieldset::class);
        $dataFieldset->shouldReceive('getOptions')
            ->andReturn($dataOptions);
        $dataFieldset->shouldReceive('setOptions')
            ->with($dataOptionsModified)
            ->once();
        $dataFieldset->shouldReceive('has')
            ->with('totCommunityLicences')
            ->andReturn(true);
        $dataFieldset->shouldReceive('get')
            ->with('totCommunityLicences')
            ->andReturn($totCommunityLicencesElement);

        $mockForm = m::mock(\Common\Form\Form::class);
        $mockForm->shouldReceive('get')
            ->with('data')
            ->andReturn($dataFieldset);

        $mockFormHelper = m::mock()
            ->shouldReceive('removeFieldList')
            ->with($mockForm, 'data', ['totAuthTrailers'])
            ->once()
            ->getMock();

        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $this->sut->alterFormForPsvLicences($mockForm, []);
    }
}
