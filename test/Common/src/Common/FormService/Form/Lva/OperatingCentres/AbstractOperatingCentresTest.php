<?php

declare(strict_types=1);

namespace CommonTest\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\OperatingCentres\AbstractOperatingCentres;
use Mockery as m;
use Common\RefData;
use Common\Form\Form;
use Common\Test\FormService\Form\Lva\OperatingCentres\OperatingCentresTestCase;

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
            'canHaveSchedule41' => true,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'operatingCentres' => ['XX'],
            'trafficArea' => ['id' => 'A', 'name' => 'THE NORTH'],
            'niFlag' => 'N',
            'possibleEnforcementAreas' => ['A', 'B'],
            'licenceType' => ['id' => RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            'isEligibleForLgv' => true,
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
            'isEligibleForLgv' => true,
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
            'isEligibleForLgv' => true,
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
    public function getForm_DisablesVehicleClassifications_WhenIsNotEligibleForLgvs()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $form = $this->sut->getForm($this->paramsForLicenceThatIsNotEligibleForLgvs());

        // Assert
        $this->assertVehicleClassificationsAreDisabledForForm($form);
    }

    /**
     * @test
     * @depends getForm_ReturnsAForm
     */
    public function getForm_DoesNotDisableVehicleClassifications_WhenIsEligibleForLgvs()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $form = $this->sut->getForm($this->paramsForLicenceThatIsEligibleForLgvs());

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
