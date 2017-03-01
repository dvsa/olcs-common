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
use Common\RefData;

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
        $mockFieldSet->shouldReceive('get')->with('trafficAreaSet')->twice()->andReturn(
            m::mock()
                ->shouldReceive('setValue')
                ->with('THE NORTH')
                ->once()
                ->shouldReceive('setLabel')
                ->with('internal.oc.traffic_area_label')
                ->once()
                ->getMock()
        );
        $mockFieldSet->shouldReceive('get')->with('enforcementArea')->once()->andReturn(
            m::mock()->shouldReceive('setValueOptions')->with(['A', 'B'])->once()->getMock()
        );

        $mockAuthService = m::mock()
            ->shouldReceive('isGranted')
            ->with(RefData::PERMISSION_INTERNAL_USER)
            ->andReturn(true)
            ->once()
            ->shouldReceive('isGranted')
            ->with(RefData::PERMISSION_INTERNAL_EDIT)
            ->andReturn(false)
            ->once()
            ->getMock();

        $this->sut->shouldReceive('getServiceLocator->get')
            ->with(\ZfcRbac\Service\AuthorizationService::class)
            ->andReturn($mockAuthService)
            ->once();

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
        $mockFieldSet->shouldReceive('get')->with('trafficAreaSet')->times(3)->andReturn(
            m::mock()
                ->shouldReceive('setValue')
                ->with('THE NORTH')
                ->once()
                ->shouldReceive('setOption')
                ->with('hint', null)
                ->once()
                ->shouldReceive('setLabel')
                ->with('internal.oc.traffic_area_label')
                ->once()
                ->getMock()
        );
        $mockFieldSet->shouldReceive('get')->with('enforcementArea')->once()->andReturn(
            m::mock()->shouldReceive('setValueOptions')->with(['A', 'B'])->once()->getMock()
        );

        $mockAuthService = m::mock()
            ->shouldReceive('isGranted')
            ->with(RefData::PERMISSION_INTERNAL_USER)
            ->andReturn(true)
            ->once()
            ->shouldReceive('isGranted')
            ->with(RefData::PERMISSION_INTERNAL_EDIT)
            ->andReturn(false)
            ->once()
            ->getMock();

        $this->sut->shouldReceive('getServiceLocator->get')
            ->with(\ZfcRbac\Service\AuthorizationService::class)
            ->andReturn($mockAuthService)
            ->once();

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
        $mockForm = m::mock(\Common\Form\Form::class)
            ->shouldReceive('get')
            ->with('data')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getOptions')
                    ->andReturn($dataOptions)
                    ->once()
                    ->shouldReceive('setOptions')
                    ->with($dataOptionsModified)
                    ->once()
                    ->getMock()
            )
            ->getMock();

        $mockFormHelper = m::mock()
            ->shouldReceive('removeFieldList')
            ->with($mockForm, 'data', ['totAuthTrailers'])
            ->once()
            ->getMock();

        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $this->sut->alterFormForPsvLicences($mockForm, []);
    }
}
