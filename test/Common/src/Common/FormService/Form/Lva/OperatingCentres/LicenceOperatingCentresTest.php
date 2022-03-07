<?php

declare(strict_types=1);

namespace CommonTest\FormService\Form\Lva\OperatingCentres;

use Common\Form\Elements\Types\Table;
use Common\FormService\Form\Lva\OperatingCentres\LicenceOperatingCentres;
use Common\FormService\FormServiceInterface;
use Common\FormService\FormServiceManager;
use Common\Service\Table\TableBuilder;
use CommonTest\Bootstrap;
use Mockery as m;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Common\Service\Helper\FormHelperService;
use Common\RefData;
use Common\Test\FormService\Form\Lva\OperatingCentres\LicenceOperatingCentresTestCase;

/**
 * @see LicenceOperatingCentres
 */
class LicenceOperatingCentresTest extends LicenceOperatingCentresTestCase
{
    /**
     * @var LicenceOperatingCentres
     */
    protected $sut;

    public function testGetForm()
    {
        $tableBuilder = m::mock();

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Table', $tableBuilder);

        $fsm = m::mock(FormServiceManager::class)->makePartial();
        $fsm->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $form = m::mock(Form::class);

        $lvaLicence = m::mock(FormServiceInterface::class);
        $lvaLicence->shouldReceive('alterForm')
            ->once()
            ->with($form);

        $fsm->setService('lva-licence', $lvaLicence);

        $mockFormHelper = m::mock(FormHelperService::class);
        $mockFormHelper->shouldReceive('createForm')
            ->once()
            ->with('Lva\OperatingCentres')
            ->andReturn($form);

        $sut = new LicenceOperatingCentres();
        $sut->setFormHelper($mockFormHelper);
        $sut->setFormServiceLocator($fsm);

        $params = [
            'operatingCentres' => [],
            'canHaveSchedule41' => false,
            'canHaveCommunityLicences' => true,
            'isPsv' => false,
            'licenceType' => ['id' => RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            'vehicleType' => ['id' => RefData::APP_VEHICLE_TYPE_MIXED],
            'totAuthLgvVehicles' => 0,
        ];

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

        $form->shouldReceive('has')
            ->with('table')
            ->andReturnTrue();

        $form->shouldReceive('get')
            ->with('table')
            ->andReturn($fieldset);

        $tableBuilder->shouldReceive('prepareTable')
            ->with('lva-operating-centres', [], [])
            ->andReturn($table);

        $mockFormHelper->shouldReceive('populateFormTable')
            ->with($fieldset, $table);

        $mockFormHelper->shouldReceive('getValidator->setMessage')
            ->with('OperatingCentreNoOfOperatingCentres.required', 'required');

        $mockFormHelper->shouldReceive('remove')
            ->once()
            ->with($form, 'dataTrafficArea');

        $totCommunityLicences = m::mock(Element::class);
        $totCommunityLicencesFieldset = m::mock(Fieldset::class);
        $totCommunityLicencesFieldset->shouldReceive('get')
            ->with('totCommunityLicences')
            ->andReturn($totCommunityLicences);

        $data = m::mock();
        $data->shouldReceive('has')
            ->with('totCommunityLicencesFieldset')
            ->andReturn(true)
            ->shouldReceive('get')
            ->with('totCommunityLicencesFieldset')
            ->andReturn($totCommunityLicencesFieldset);

        $mockFormHelper->shouldReceive('disableElement')
            ->once()
            ->with($form, 'data->totCommunityLicencesFieldset->totCommunityLicences');

        $mockFormHelper->shouldReceive('lockElement')
            ->once()
            ->with($totCommunityLicences, 'community-licence-changes-contact-office');

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($data);

        $this->assertSame($form, $sut->getForm($params));
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
        $result = $this->sut->getForm($this->paramsForLicence());

        // Assert
        $this->assertInstanceOf(Form::class, $result);
    }

    /**
     * @test
     * @depends getForm_ReturnsAForm
     */
    public function getForm_DisablesVehicleClassifications_WhenLicenceLgvsAreNull()
    {
        // Setup
        $this->setUpSut();
        $params = $this->paramsForMixedLicenceWithoutLgv();

        // Execute
        $result = $this->sut->getForm($params);

        // Assert
        $this->assertVehicleClassificationsAreDisabledForForm($result);
    }

    protected function setUpSut()
    {
        $this->sut = new LicenceOperatingCentres();
        $this->sut->setFormHelper($this->formHelper());
        $this->sut->setFormServiceLocator($this->formServiceManager());
    }
}
