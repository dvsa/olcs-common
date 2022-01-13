<?php

declare(strict_types=1);

namespace CommonTest\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\OperatingCentres\AbstractOperatingCentres;
use Mockery as m;
use Common\RefData;
use Common\Form\Elements\Types\Table;
use Common\Form\Elements\Validators\TableRequiredValidator;
use Common\Form\Form;
use Common\Service\Table\TableBuilder;
use Common\Test\FormService\Form\Lva\OperatingCentres\OperatingCentresTestCase;
use Laminas\Form\Fieldset;
use Laminas\Validator\Between;

/**
 * @covers \Common\FormService\Form\Lva\OperatingCentres\AbstractOperatingCentres
 */
class AbstractOperatingCentresTest extends OperatingCentresTestCase
{
    protected const COMMUNITY_LICENCES_FIELDSET_NAME = 'totCommunityLicencesFieldset';
    protected const COMMUNITY_LICENCES_FIELD_NAME = 'totCommunityLicences';
    protected const TRAILERS_FIELDSET_NAME = 'totAuthTrailersFieldset';
    protected const COMMUNITY_LICENCE_FIELD_PSV_LABEL = 'application_operating-centres_authorisation.data.totCommunityLicences.psv';
    protected const A_HINT = 'A HINT';
    protected const A_HINT_WITH_PSV_MODIFIER = 'A HINT.psv';
    protected const FORM_NAME = 'Lva\OperatingCentres';

    /**
     * @var  AbstractOperatingCentres
     */
    protected $sut;

    public function testAlterFormWithTrafficArea()
    {
        $this->sut = m::mock(AbstractOperatingCentres::class);
        $params = [
            'canHaveSchedule41' => false,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'operatingCentres' => ['XX'],
            'trafficArea' => ['id' => 'A', 'name' => 'THE NORTH'],
            'niFlag' => 'N',
            'possibleEnforcementAreas' => ['A', 'B'],
            'licenceType' => ['id' => RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            'vehicleType' => ['id' => RefData::APP_VEHICLE_TYPE_MIXED],
            'totAuthLgvVehicles' => 0,
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

        $columns = [
            'noOfVehiclesRequired' => [
                'title' => 'vehicles',
            ]
        ];

        $table = m::mock(TableBuilder::class);
        $table->shouldReceive('removeAction')
            ->with('schedule41')
            ->once()
            ->shouldReceive('getColumns')
            ->withNoArgs()
            ->andReturn($columns)
            ->shouldReceive('setColumns')
            ->with(
                [
                    'noOfVehiclesRequired' => [
                        'title' => 'application_operating-centres_authorisation.table.hgvs',
                    ]
                ]
            )
            ->once();

        $tableElement = m::mock(Table::class);
        $tableElement->shouldReceive('getTable')
            ->withNoArgs()
            ->andReturn($table);

        $fieldset = m::mock(Fieldset::class);
        $fieldset->shouldReceive('get')
            ->with('table')
            ->andReturn($tableElement);

        $mockForm = m::mock(\Common\Form\Form::class);
        $mockForm->shouldReceive('has')
            ->with('table')
            ->andReturnTrue();
        $mockForm->shouldReceive('get')
            ->with('table')
            ->andReturn($fieldset);
        $mockForm->shouldReceive('get')->with('dataTrafficArea')->once()->andReturn($mockFieldSet);

        $mockFormHelper = m::mock();
        $mockFormHelper->shouldReceive('getValidator->setMessage');
        $mockFormHelper->shouldReceive('remove')->with($mockForm, 'dataTrafficArea->trafficArea')->once();

        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $this->sut->alterForm($mockForm, $params);
    }

    public function testAlterFormWithTrafficAreaNi()
    {
        $this->sut = m::mock(AbstractOperatingCentres::class);
        $params = [
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'operatingCentres' => ['XX'],
            'trafficArea' => ['id' => 'A', 'name' => 'THE NORTH'],
            'niFlag' => 'Y',
            'possibleEnforcementAreas' => ['A', 'B'],
            'licenceType' => ['id' => RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            'vehicleType' => ['id' => RefData::APP_VEHICLE_TYPE_MIXED],
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
        $mockForm->shouldReceive('has')
            ->with('table')
            ->andReturnFalse();
        $mockForm->shouldReceive('get')->with('dataTrafficArea')->twice()->andReturn($mockFieldSet);

        $mockFormHelper = m::mock();
        $mockFormHelper->shouldReceive('remove')->with($mockForm, 'dataTrafficArea->trafficArea')->once();
        $mockFormHelper->shouldReceive('getValidator->setMessage');
        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $this->sut->alterForm($mockForm, $params);
    }

    public function testAlterFormWithOutTrafficArea()
    {
        $this->sut = m::mock(AbstractOperatingCentres::class);
        $mockForm = m::mock(\Common\Form\Form::class);

        $params = [
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'operatingCentres' => ['XX'],
            'trafficArea' => null,
            'niFlag' => 'N',
            'possibleTrafficAreas' => ['A', 'B'],
            'possibleEnforcementAreas' => ['A', 'B'],
            'licenceType' => ['id' => RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            'vehicleType' => ['id' => RefData::APP_VEHICLE_TYPE_MIXED],
        ];

        $mockFormHelper = m::mock();
        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $mockFormHelper->shouldReceive('getValidator->setMessage');

        $mockForm->shouldReceive('has')
            ->with('table')
            ->andReturnFalse();

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

    public function testAlterFormForLgv()
    {
        $this->sut = m::mock(AbstractOperatingCentres::class);
        $mockForm = m::mock(\Common\Form\Form::class);

        $params = [
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'operatingCentres' => [],
            'trafficArea' => null,
            'niFlag' => 'N',
            'possibleTrafficAreas' => ['A', 'B'],
            'possibleEnforcementAreas' => ['A', 'B'],
            'licenceType' => ['id' => RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            'vehicleType' => ['id' => RefData::APP_VEHICLE_TYPE_LGV],
        ];

        $lgvBetweenValidator = m::mock(Between::class);
        $lgvBetweenValidator->shouldReceive('setMin')
            ->with(1)
            ->once()
            ->shouldReceive('getMax')
            ->withNoArgs()
            ->once()
            ->andReturn(10);

        $comLicBetweenValidator = m::mock(Between::class);
        $comLicBetweenValidator->shouldReceive('setMax')
                ->with(10)
                ->once();

        $tableRequiredValidator = m::mock(TableRequiredValidator::class);
        $tableRequiredValidator->shouldReceive('setMessage')
            ->with('OperatingCentreNoOfOperatingCentres.required', 'required')
            ->once();

        $mockFormHelper = m::mock();
        $mockFormHelper
            ->shouldReceive('remove')
            ->with($mockForm, 'table')
            ->once()
            ->shouldReceive('remove')
            ->with($mockForm, 'data->totAuthHgvVehiclesFieldset')
            ->once()
            ->shouldReceive('remove')
            ->with($mockForm, 'data->totAuthTrailersFieldset')
            ->once()
            ->shouldReceive('getValidator')
            ->with($mockForm, 'data->totAuthLgvVehiclesFieldset->totAuthLgvVehicles', Between::class)
            ->once()
            ->andReturn($lgvBetweenValidator)
            ->shouldReceive('getValidator')
            ->with($mockForm, 'data->totCommunityLicencesFieldset->totCommunityLicences', Between::class)
            ->once()
            ->andReturn($comLicBetweenValidator)
            ->shouldReceive('getValidator')
            ->with($mockForm, 'table->rows', 'Common\Form\Elements\Validators\TableRequiredValidator')
            ->once()
            ->andReturn($tableRequiredValidator);

        $this->sut->shouldReceive('getFormHelper')->andReturn($mockFormHelper);

        $mockForm->shouldReceive('has')
            ->with('table')
            ->andReturnFalse();

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

    /**
     * @test
     */
    public function getForm_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'getForm']);
    }

    /**
     * @test
     * @depends getForm_IsCallable
     */
    public function getForm_ReturnsAForm()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->getForm($this->paramsForGoodsLicence());

        // Assert
        $this->assertInstanceOf(Form::class, $result);
    }

    /**
     * @test
     * @depends getForm_ReturnsAForm
     */
    public function getForm_DisablesVehicleClassifications_WhenIsHgv()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $form = $this->sut->getForm($this->paramsForHgvLicence());

        // Assert
        $this->assertVehicleClassificationsAreDisabledForForm($form);
    }

    /**
     * @test
     * @depends getForm_ReturnsAForm
     */
    public function getForm_DoesNotDisableVehicleClassifications_WhenIsMixedWithLgv()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $form = $this->sut->getForm($this->paramsForMixedLicenceWithLgv());

        // Assert
        $this->assertVehicleClassificationsAreEnabledForForm($form);
    }

    /**
     * @test
     * @depends getForm_ReturnsAForm
     */
    public function getForm_DoesNotRemoveTotCommunityLicencesFieldset_WhenCanHaveCommunityLicences()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $form = $this->sut->getForm($this->paramsForLicenceThatAreEligibleForCommunityLicences());
        $fieldset = $form->get('data');

        // Assert
        $this->assertTrue($fieldset->has(static::COMMUNITY_LICENCES_FIELDSET_NAME));
    }

    /**
     * @test
     * @depends getForm_ReturnsAForm
     */
    public function getForm_RemovesTotCommunityLicencesFieldset_WhenCantHaveCommunityLicences()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $form = $this->sut->getForm($this->paramsForLicenceThatAreNotEligibleForCommunityLicences());
        $fieldset = $form->get('data');

        // Assert
        $this->assertFalse($fieldset->has(static::COMMUNITY_LICENCES_FIELDSET_NAME));
    }

    /**
     * @test
     * @depends getForm_DoesNotRemoveTotCommunityLicencesFieldset_WhenCanHaveCommunityLicences
     */
    public function getForm_SetsLabelForTotCommunityLicencesFieldset_ForPsvs()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $form = $this->sut->getForm($this->paramsForPsvLicenceThatAreEligibleForCommunityLicences());
        $fieldset = $form->get('data')->get(static::COMMUNITY_LICENCES_FIELDSET_NAME);

        // Assert
        $this->assertSame('', $fieldset->getLabel());
    }

    /**
     * @test
     * @depends getForm_DoesNotRemoveTotCommunityLicencesFieldset_WhenCanHaveCommunityLicences
     */
    public function getForm_SetsLabelForTotCommunityLicencesField_ForPsvs()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $form = $this->sut->getForm($this->paramsForPsvLicenceThatAreEligibleForCommunityLicences());
        $field = $form->get('data')->get(static::COMMUNITY_LICENCES_FIELDSET_NAME)->get(static::COMMUNITY_LICENCES_FIELD_NAME);

        // Assert
        $this->assertSame(static::COMMUNITY_LICENCE_FIELD_PSV_LABEL, $field->getLabel());
    }

    /**
     * @test
     * @depends getForm_ReturnsAForm
     */
    public function getForm_AppendsModifierToDataFieldsetHint_ForPsvs()
    {
        // Setup
        $form = $this->formHelper()->createForm(static::FORM_NAME);
        $form->get('data')->setOption('hint', static::A_HINT);
        $this->overrideFormHelperWithMock();
        $this->formHelper()->allows('createForm')->with(static::FORM_NAME)->andReturn($form);
        $this->setUpSut();
        $params = array_merge($this->paramsForPsvLicence(), ['hint' => static::A_HINT]);

        // Execute
        $form = $this->sut->getForm($params);
        $fieldset = $form->get('data');

        // Assert
        $this->assertSame(static::A_HINT_WITH_PSV_MODIFIER, $fieldset->getOption('hint'));
    }

    /**
     * @test
     * @depends getForm_ReturnsAForm
     */
    public function getForm_RemovesTotAuthTrailersFieldset_WhenIsPsv()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $form = $this->sut->getForm($this->paramsForPsvLicence());
        $fieldset = $form->get('data');

        // Assert
        $this->assertFalse($fieldset->has(static::TRAILERS_FIELDSET_NAME));
    }

    protected function setUpSut()
    {
        $this->sut = new class extends AbstractOperatingCentres {

        };
        $this->sut->setFormHelper($this->formHelper());
        $this->sut->setFormServiceLocator($this->formServiceManager());
    }
}
