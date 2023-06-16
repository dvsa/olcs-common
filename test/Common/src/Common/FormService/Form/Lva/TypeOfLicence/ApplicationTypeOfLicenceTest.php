<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\FormService\Form\Lva\Application;
use Common\FormService\FormServiceManager;
use Common\RefData;
use Common\Service\Helper\FormHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\InputFilter\InputFilter;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use ZfcRbac\Service\AuthorizationService;

class ApplicationTypeOfLicenceTest extends MockeryTestCase
{
    /** @var ApplicationTypeOfLicence */
    protected $sut;

    /** @var  m\MockInterface|FormServiceManager */
    protected $fsm;
    /** @var  m\MockInterface|FormHelperService */
    protected $fh;

    public function setUp(): void
    {
        $this->fsm = m::mock(FormServiceManager::class)->makePartial();
        $this->fh = m::mock(FormHelperService::class)->makePartial();
        $this->authService = m::mock(AuthorizationService::class);
        $this->sut = new ApplicationTypeOfLicence($this->fh, $this->authService, $this->fsm);
    }

    public function testGetForm()
    {
        $mockForm = m::mock(Form::class);

        $this->fh->shouldReceive('createForm')
            ->once()
            ->with('Lva\TypeOfLicence')
            ->andReturn($mockForm);

        $appService = m::mock(Application::class);
        $this->fsm->setService('lva-application', $appService);

        $appService->shouldReceive('alterForm')
            ->once()
            ->with($mockForm);

        $form = $this->sut->getForm([]);

        $this->assertSame($mockForm, $form);
    }

    /**
     * Test set and lock operator location
     *
     * @dataProvider lockOperatorLocationProvider
     * @param string $message
     * @param string $locationValue
     * @param string $location
     */
    public function testSetAndLockOperatorLocation($message, $location, $locationValue)
    {
        $mockOperatorLocation = m::mock(\Laminas\Form\Element::class)
            ->shouldReceive('setValue')
            ->with($locationValue)
            ->once()
            ->getMock();

        $mockForm = m::mock(Form::class)
            ->shouldReceive('get')
            ->with('type-of-licence')
            ->once()
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('operator-location')
                ->once()
                ->andReturn($mockOperatorLocation)
                ->getMock()
            )
            ->getMock();

        $this->fh->shouldReceive('disableElement')
            ->with($mockForm, 'type-of-licence->operator-location')
            ->once()
            ->shouldReceive('lockElement')
            ->with($mockOperatorLocation, $message)
            ->once()
            ->getMock();

        $this->sut->setAndLockOperatorLocation($mockForm, $location);
    }

    /**
     * Lock operator location provider
     */
    public function lockOperatorLocationProvider()
    {
        return [
            ['alternative-operator-location-lock-message-ni', 'NI', 'Y'],
            ['alternative-operator-location-lock-message-gb', 'GB', 'N']
        ];
    }

    public function testMaybeAlterFormForNi()
    {
        $mockOperatorLocation = m::mock(\Laminas\Form\Element::class)
            ->shouldReceive('getValue')
            ->andReturn('Y')
            ->once()
            ->getMock();

        $mockForm = m::mock(Form::class)
            ->shouldReceive('get')
            ->with('type-of-licence')
            ->once()
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('operator-location')
                    ->once()
                    ->andReturn($mockOperatorLocation)
                    ->getMock()
            )
            ->shouldReceive('getInputFilter')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('type-of-licence')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('get')
                    ->with('operator-type')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('setRequired')
                        ->with(false)
                        ->once()
                        ->getMock()
                    )
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->sut->maybeAlterFormForNi($mockForm);
    }

    /**
     * @dataProvider dpMaybeAlterFormForGoodsStandardInternationalNoChange
     */
    public function testMaybeAlterFormForGoodsStandardInternationalNoChange(
        $operatorLocationValue,
        $operatorTypeValue,
        $licenceTypeValue,
        $vehicleTypeValue
    ) {
        $licenceType = m::mock(Element::class);
        $licenceType->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($licenceTypeValue);

        $operatorLocation = m::mock(Element::class);
        $operatorLocation->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($operatorLocationValue);

        $operatorType = m::mock(Element::class);
        $operatorType->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($operatorTypeValue);

        $vehicleType = m::mock(Element::class);
        $vehicleType->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($vehicleTypeValue);

        $ltypSiContentFieldset = m::mock(Fieldset::class);
        $ltypSiContentFieldset->shouldReceive('get')
            ->with('vehicle-type')
            ->andReturn($vehicleType);

        $licenceTypeFieldset = m::mock(Fieldset::class);
        $licenceTypeFieldset->shouldReceive('get')
            ->with('licence-type')
            ->andReturn($licenceType);
        $licenceTypeFieldset->shouldReceive('get')
            ->with('ltyp_siContent')
            ->andReturn($ltypSiContentFieldset);

        $typeOfLicenceFieldset = m::mock(Fieldset::class);
        $typeOfLicenceFieldset->shouldReceive('get')
            ->with('licence-type')
            ->andReturn($licenceTypeFieldset);
        $typeOfLicenceFieldset->shouldReceive('get')
            ->with('operator-location')
            ->andReturn($operatorLocation);
        $typeOfLicenceFieldset->shouldReceive('get')
            ->with('operator-type')
            ->andReturn($operatorType);

        $form = m::mock(Form::class);
        $form->shouldReceive('get')
            ->with('type-of-licence')
            ->andReturn($typeOfLicenceFieldset);

        $this->sut->maybeAlterFormForGoodsStandardInternational($form);
    }

    public function dpMaybeAlterFormForGoodsStandardInternationalNoChange()
    {
        return [
            'ni, psv, standard international, lgv' => [
                'Y',
                RefData::LICENCE_CATEGORY_PSV,
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                RefData::APP_VEHICLE_TYPE_LGV,
            ],
            'ni, goods, standard international, lgv' => [
                'Y',
                RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                RefData::APP_VEHICLE_TYPE_LGV,
            ],
            'gb, goods, standard international, lgv' => [
                'N',
                RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                RefData::APP_VEHICLE_TYPE_LGV,
            ],
        ];
    }

    /**
     * @dataProvider dpMaybeAlterFormForGoodsStandardInternationalRemoveDeclarationRequirement
     */
    public function testMaybeAlterFormForGoodsStandardInternationalRemoveDeclarationRequirement(
        $operatorLocationValue,
        $operatorTypeValue,
        $licenceTypeValue,
        $vehicleTypeValue
    ) {
        $licenceType = m::mock(Element::class);
        $licenceType->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($licenceTypeValue);

        $operatorLocation = m::mock(Element::class);
        $operatorLocation->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($operatorLocationValue);

        $operatorType = m::mock(Element::class);
        $operatorType->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($operatorTypeValue);

        $vehicleType = m::mock(Element::class);
        $vehicleType->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($vehicleTypeValue);

        $ltypSiContentFieldset = m::mock(Fieldset::class);
        $ltypSiContentFieldset->shouldReceive('get')
            ->with('vehicle-type')
            ->andReturn($vehicleType);

        $licenceTypeFieldset = m::mock(Fieldset::class);
        $licenceTypeFieldset->shouldReceive('get')
            ->with('licence-type')
            ->andReturn($licenceType);
        $licenceTypeFieldset->shouldReceive('get')
            ->with('ltyp_siContent')
            ->andReturn($ltypSiContentFieldset);

        $typeOfLicenceFieldset = m::mock(Fieldset::class);
        $typeOfLicenceFieldset->shouldReceive('get')
            ->with('licence-type')
            ->andReturn($licenceTypeFieldset);
        $typeOfLicenceFieldset->shouldReceive('get')
            ->with('operator-location')
            ->andReturn($operatorLocation);
        $typeOfLicenceFieldset->shouldReceive('get')
            ->with('operator-type')
            ->andReturn($operatorType);

        $ltypSiContentInputFilter = m::mock(InputFilter::class);
        $ltypSiContentInputFilter->shouldReceive('remove')
            ->with('lgv-declaration')
            ->once();

        $licenceTypeInputFilter = m::mock(InputFilter::class);
        $licenceTypeInputFilter->shouldReceive('get')
            ->with('ltyp_siContent')
            ->andReturn($ltypSiContentInputFilter);

        $typeOfLicenceInputFilter = m::mock(InputFilter::class);
        $typeOfLicenceInputFilter->shouldReceive('get')
            ->with('licence-type')
            ->andReturn($licenceTypeInputFilter);

        $formInputFilter = m::mock(InputFilter::class);
        $formInputFilter->shouldReceive('get')
            ->with('type-of-licence')
            ->andReturn($typeOfLicenceInputFilter);

        $form = m::mock(Form::class);
        $form->shouldReceive('get')
            ->with('type-of-licence')
            ->andReturn($typeOfLicenceFieldset);
        $form->shouldReceive('getInputFilter')
            ->withNoArgs()
            ->andReturn($formInputFilter);

        $this->sut->maybeAlterFormForGoodsStandardInternational($form);
    }

    public function dpMaybeAlterFormForGoodsStandardInternationalRemoveDeclarationRequirement()
    {
        return [
            'ni, psv, standard international, mixed' => [
                'Y',
                RefData::LICENCE_CATEGORY_PSV,
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                RefData::APP_VEHICLE_TYPE_MIXED,
            ],
            'ni, goods, standard international, mixed' => [
                'Y',
                RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                RefData::APP_VEHICLE_TYPE_MIXED,
            ],
            'gb, goods, standard international, mixed' => [
                'N',
                RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                RefData::APP_VEHICLE_TYPE_MIXED,
            ],
        ];
    }

    /**
     * @dataProvider dpMaybeAlterFormForGoodsStandardInternationalRemoveVehicleTypeAndDeclarationRequirement
     */
    public function testMaybeAlterFormForGoodsStandardInternationalRemoveVehicleTypeAndDeclarationRequirement(
        $operatorLocationValue,
        $operatorTypeValue,
        $licenceTypeValue
    ) {
        $vehicleTypeValue = '';

        $licenceType = m::mock(Element::class);
        $licenceType->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($licenceTypeValue);

        $operatorLocation = m::mock(Element::class);
        $operatorLocation->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($operatorLocationValue);

        $operatorType = m::mock(Element::class);
        $operatorType->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($operatorTypeValue);

        $vehicleType = m::mock(Element::class);
        $vehicleType->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($vehicleTypeValue);

        $ltypSiContentFieldset = m::mock(Fieldset::class);
        $ltypSiContentFieldset->shouldReceive('get')
            ->with('vehicle-type')
            ->andReturn($vehicleType);

        $licenceTypeFieldset = m::mock(Fieldset::class);
        $licenceTypeFieldset->shouldReceive('get')
            ->with('licence-type')
            ->andReturn($licenceType);
        $licenceTypeFieldset->shouldReceive('get')
            ->with('ltyp_siContent')
            ->andReturn($ltypSiContentFieldset);

        $typeOfLicenceFieldset = m::mock(Fieldset::class);
        $typeOfLicenceFieldset->shouldReceive('get')
            ->with('licence-type')
            ->andReturn($licenceTypeFieldset);
        $typeOfLicenceFieldset->shouldReceive('get')
            ->with('operator-location')
            ->andReturn($operatorLocation);
        $typeOfLicenceFieldset->shouldReceive('get')
            ->with('operator-type')
            ->andReturn($operatorType);

        $licenceTypeInputFilter = m::mock(InputFilter::class);
        $licenceTypeInputFilter->shouldReceive('remove')
            ->with('ltyp_siContent')
            ->once();

        $typeOfLicenceInputFilter = m::mock(InputFilter::class);
        $typeOfLicenceInputFilter->shouldReceive('get')
            ->with('licence-type')
            ->andReturn($licenceTypeInputFilter);

        $formInputFilter = m::mock(InputFilter::class);
        $formInputFilter->shouldReceive('get')
            ->with('type-of-licence')
            ->andReturn($typeOfLicenceInputFilter);

        $form = m::mock(Form::class);
        $form->shouldReceive('get')
            ->with('type-of-licence')
            ->andReturn($typeOfLicenceFieldset);
        $form->shouldReceive('getInputFilter')
            ->withNoArgs()
            ->andReturn($formInputFilter);

        $this->sut->maybeAlterFormForGoodsStandardInternational($form);
    }

    public function dpMaybeAlterFormForGoodsStandardInternationalRemoveVehicleTypeAndDeclarationRequirement()
    {
        return [
            'gb, goods, restricted' => [
                'N',
                RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                RefData::LICENCE_TYPE_RESTRICTED,
            ],
            'gb, goods, standard national' => [
                'N',
                RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                RefData::LICENCE_TYPE_STANDARD_NATIONAL,
            ],
            'gb, goods, special restricted' => [
                'N',
                RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                RefData::LICENCE_TYPE_SPECIAL_RESTRICTED,
            ],
            'gb, psv, restricted' => [
                'N',
                RefData::LICENCE_CATEGORY_PSV,
                RefData::LICENCE_TYPE_RESTRICTED,
            ],
            'gb, psv, standard national' => [
                'N',
                RefData::LICENCE_CATEGORY_PSV,
                RefData::LICENCE_TYPE_STANDARD_NATIONAL,
            ],
            'gb, psv, special restricted' => [
                'N',
                RefData::LICENCE_CATEGORY_PSV,
                RefData::LICENCE_TYPE_SPECIAL_RESTRICTED,
            ],
            'ni, goods, restricted' => [
                'Y',
                RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                RefData::LICENCE_TYPE_RESTRICTED,
            ],
            'ni, goods, standard national' => [
                'Y',
                RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                RefData::LICENCE_TYPE_STANDARD_NATIONAL,
            ],
            'ni, goods, special restricted' => [
                'Y',
                RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                RefData::LICENCE_TYPE_SPECIAL_RESTRICTED,
            ],
            'ni, psv, restricted' => [
                'Y',
                RefData::LICENCE_CATEGORY_PSV,
                RefData::LICENCE_TYPE_RESTRICTED,
            ],
            'ni, psv, standard national' => [
                'Y',
                RefData::LICENCE_CATEGORY_PSV,
                RefData::LICENCE_TYPE_STANDARD_NATIONAL,
            ],
            'ni, psv, special restricted' => [
                'Y',
                RefData::LICENCE_CATEGORY_PSV,
                RefData::LICENCE_TYPE_SPECIAL_RESTRICTED,
            ],
        ];
    }
}
