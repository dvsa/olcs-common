<?php

namespace CommonTest\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\OperatingCentres\VariationOperatingCentres;
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
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class AbstractOperatingCentresTest extends MockeryTestCase
{
    public function setUp()
    {
        $this->sut = m::mock(\Common\FormService\Form\Lva\OperatingCentres\AbstractOperatingCentres::class);
    }

    public function testAlterFormWithTrafficArea()
    {
        $mockForm = m::mock(\Common\Form\Form::class);

        $params = [
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'operatingCentres' => ['XX'],
            'trafficArea' => ['id' => 'A', 'name' => 'THE NORTH'],
            'niFlag' => 'N',
            'possibleEnforcementAreas' => ['A', 'B']
        ];

        $mockFormHelper = m::mock();
        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $mockFormHelper->shouldReceive('getValidator->setMessage');

        $mockFieldSet = m::mock();
        $mockForm->shouldReceive('get')->with('dataTrafficArea')->once()->andReturn($mockFieldSet);

        $mockFormHelper->shouldReceive('remove')->with($mockForm, 'dataTrafficArea->trafficArea')->once();
        $mockFieldSet->shouldReceive('get')->with('trafficAreaSet')->once()->andReturn(
            m::mock()->shouldReceive('setValue')->with('THE NORTH')->once()->getMock()
        );
        $mockFieldSet->shouldReceive('get')->with('enforcementArea')->once()->andReturn(
            m::mock()->shouldReceive('setValueOptions')->with(['A', 'B'])->once()->getMock()
        );

        $this->sut->alterForm($mockForm, $params);
    }

    public function testAlterFormWithTrafficAreaNi()
    {
        $mockForm = m::mock(\Common\Form\Form::class);

        $params = [
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'operatingCentres' => ['XX'],
            'trafficArea' => ['id' => 'A', 'name' => 'THE NORTH'],
            'niFlag' => 'Y',
            'possibleEnforcementAreas' => ['A', 'B']
        ];

        $mockFormHelper = m::mock();
        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $mockFormHelper->shouldReceive('getValidator->setMessage');

        $mockFieldSet = m::mock();
        $mockForm->shouldReceive('get')->with('dataTrafficArea')->twice()->andReturn($mockFieldSet);

        $mockFormHelper->shouldReceive('remove')->with($mockForm, 'dataTrafficArea->trafficArea')->once();
        $mockFieldSet->shouldReceive('get')->with('trafficAreaSet')->twice()->andReturn(
            m::mock()
                ->shouldReceive('setValue')->with('THE NORTH')->once()
                ->shouldReceive('setOption')->with('hint', null)->once()
                ->getMock()
        );
        $mockFieldSet->shouldReceive('get')->with('enforcementArea')->once()->andReturn(
            m::mock()->shouldReceive('setValueOptions')->with(['A', 'B'])->once()->getMock()
        );

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
}
