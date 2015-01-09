<?php

/**
 * Licence Operating Centre Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\LicenceOperatingCentreAdapter;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Helper\FormHelperService;

/**
 * Licence Operating Centre Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentreAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $controller;

    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock('\Zend\Mvc\Controller\AbstractController');

        $this->sut = new LicenceOperatingCentreAdapter();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    public function testGetDocumentProperties()
    {
        $mockLicenceAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceAdapter);

        $mockLicenceAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn(3);

        $response = $this->sut->getDocumentProperties();

        $this->assertEquals(
            ['licence' => 3],
            $response
        );
    }

    public function testAddMessages()
    {
        // Stubbed data
        $licenceId = 4;

        // Mocked services
        $mockLicenceAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceAdapter);
        $mockFlashMessenger = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);
        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        // Expectations
        $mockLicenceAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $this->controller->shouldReceive('url->fromRoute')
            ->with('create_variation', ['licence' => 4])
            ->andReturn('URL');

        $mockTranslator->shouldReceive('translateReplace')
            ->with('variation-application-message', ['URL'])
            ->andReturn('TRANSLATED');

        $mockFlashMessenger->shouldReceive('addCurrentMessage')
            ->with('TRANSLATED', 'info');

        $this->sut->addMessages();
    }

    public function testAlterActionFormForGoodsWithoutVehicleOrTrailerElement()
    {
        // Stubbed data
        $stubbedTolData = [
            'niFlag' => 'N',
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
        ];
        $licenceId = 5;
        $stubbedTrafficArea = [
            'foo' => 'bar'
        ];

        // Mocked dependencies
        $mockForm = m::mock('\Zend\Form\Form');
        $mockInputFilter = m::mock();

        // Mocked services
        $mockLicenceEntityService = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntityService);
        $mockLicenceAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceAdapter);
        $mockValidator = m::mock();
        $this->sm->setService('postcodeTrafficAreaValidator', $mockValidator);
        $mockLocService = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLocService);

        // Expectations
        $mockLicenceEntityService->shouldReceive('getTypeOfLicenceData')
            ->andReturn($stubbedTolData);

        $mockForm->shouldReceive('get')
            ->with('data')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('sufficientParking')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('permission')
            ->andReturnSelf();

        $mockLicenceAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntityService->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn($stubbedTrafficArea);

        $mockLocService->shouldReceive('getOperatingCentresCount')
            ->andReturn(['Count' => 1]);

        $mockValidator->shouldReceive('setNiFlag')
            ->with('N')
            ->shouldReceive('setOperatingCentresCount')
            ->with(1)
            ->shouldReceive('setTrafficArea')
            ->with($stubbedTrafficArea);

        $mockInputFilter->shouldReceive('get')
            ->with('address')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('postcode')
            ->andReturnSelf()
            ->shouldReceive('setRequired')
            ->with(false)
            ->andReturnSelf()
            ->shouldReceive('getValidatorChain')
            ->andReturn(
                m::mock()
                ->shouldReceive('attach')
                ->with($mockValidator)
                ->getMock()
            );

        $mockForm->shouldReceive('getInputFilter')
            ->andReturn($mockInputFilter);

        $mockInputFilter->shouldReceive('get')
            ->with('data')
            ->andReturn(
                m::mock()
                ->shouldReceive('has')
                ->andReturn(false)
                ->getMock()
            );

        $this->assertSame($mockForm, $this->sut->alterActionForm($mockForm));
    }

    public function testAlterActionFormForGoodsWithoutVehicleOrTrailerElementWithoutTrafficArea()
    {
        // Stubbed data
        $stubbedTolData = [
            'niFlag' => 'N',
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
        ];
        $licenceId = 5;
        $stubbedTrafficArea = null;

        // Mocked dependencies
        $mockForm = m::mock('\Zend\Form\Form');
        $mockInputFilter = m::mock();

        // Mocked services
        $mockLicenceEntityService = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntityService);
        $mockLicenceAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceAdapter);
        $mockValidator = m::mock();
        $this->sm->setService('postcodeTrafficAreaValidator', $mockValidator);
        $mockLocService = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLocService);

        // Expectations
        $mockLicenceEntityService->shouldReceive('getTypeOfLicenceData')
            ->andReturn($stubbedTolData);

        $mockForm->shouldReceive('get')
            ->with('data')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('sufficientParking')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('permission')
            ->andReturnSelf();

        $mockLicenceAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntityService->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn($stubbedTrafficArea);

        $mockLocService->shouldReceive('getOperatingCentresCount')
            ->andReturn(['Count' => 1]);

        $mockValidator->shouldReceive('setNiFlag')
            ->with('N')
            ->shouldReceive('setOperatingCentresCount')
            ->with(1)
            ->shouldReceive('setTrafficArea')
            ->with($stubbedTrafficArea);

        $mockInputFilter->shouldReceive('get')
            ->with('address')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('postcode')
            ->andReturnSelf()
            ->shouldReceive('setRequired')
            ->with(false)
            ->andReturnSelf()
            ->shouldReceive('getValidatorChain')
            ->andReturn(
                m::mock()
                ->shouldReceive('attach')
                ->with($mockValidator)
                ->getMock()
            );

        $mockForm->shouldReceive('getInputFilter')
            ->andReturn($mockInputFilter);

        $mockInputFilter->shouldReceive('get')
            ->with('data')
            ->andReturn(
                m::mock()
                ->shouldReceive('has')
                ->andReturn(false)
                ->getMock()
            );

        $mockForm->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                ->shouldReceive('has')
                ->with('addAnother')
                ->andReturn(true)
                ->shouldReceive('remove')
                ->with('addAnother')
                ->getMock()
            );

        $this->assertSame($mockForm, $this->sut->alterActionForm($mockForm));
    }

    public function testAlterActionFormForPsvWithoutVehicleOrTrailerElement()
    {
        // Stubbed data
        $stubbedTolData = [
            'niFlag' => 'N',
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV
        ];
        $licenceId = 5;
        $stubbedTrafficArea = [
            'foo' => 'bar'
        ];

        // Mocked dependencies
        $mockForm = m::mock('\Zend\Form\Form');
        $mockInputFilter = m::mock();

        // Mocked services
        $mockLicenceEntityService = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntityService);
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockLicenceAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceAdapter);
        $mockValidator = m::mock();
        $this->sm->setService('postcodeTrafficAreaValidator', $mockValidator);
        $mockLocService = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLocService);

        // Expectations
        $mockLicenceEntityService->shouldReceive('getTypeOfLicenceData')
            ->andReturn($stubbedTolData);

        $mockForm->shouldReceive('get')
            ->with('data')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('sufficientParking')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('permission')
            ->andReturnSelf();

        $mockFormHelper->shouldReceive('remove')
            ->with($mockForm, 'data->noOfTrailersRequired')
            ->shouldReceive('remove')
            ->with($mockForm, 'advertisements')
            ->shouldReceive('alterElementLabel')
            ->times(3)
            ->with($mockForm, '-psv', FormHelperService::ALTER_LABEL_APPEND);

        $mockLicenceAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntityService->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn($stubbedTrafficArea);

        $mockLocService->shouldReceive('getOperatingCentresCount')
            ->andReturn(['Count' => 1]);

        $mockValidator->shouldReceive('setNiFlag')
            ->with('N')
            ->shouldReceive('setOperatingCentresCount')
            ->with(1)
            ->shouldReceive('setTrafficArea')
            ->with($stubbedTrafficArea);

        $mockInputFilter->shouldReceive('get')
            ->with('address')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('postcode')
            ->andReturnSelf()
            ->shouldReceive('setRequired')
            ->with(false)
            ->andReturnSelf()
            ->shouldReceive('getValidatorChain')
            ->andReturn(
                m::mock()
                ->shouldReceive('attach')
                ->with($mockValidator)
                ->getMock()
            );

        $mockForm->shouldReceive('getInputFilter')
            ->andReturn($mockInputFilter);

        $mockInputFilter->shouldReceive('get')
            ->with('data')
            ->andReturn(
                m::mock()
                ->shouldReceive('has')
                ->andReturn(false)
                ->getMock()
            );

        $this->assertSame($mockForm, $this->sut->alterActionForm($mockForm));
    }

    public function testAlterActionFormForPsv()
    {
        // Stubbed data
        $stubbedTolData = [
            'niFlag' => 'N',
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV
        ];
        $licenceId = 5;
        $stubbedTrafficArea = [
            'foo' => 'bar'
        ];
        $childId = 4;
        $stubbedVehicleAuths = [
            'noOfVehiclesRequired' => 123,
            'noOfTrailersRequired' => 456
        ];

        // Mocked dependencies
        $mockForm = m::mock('\Zend\Form\Form');
        $mockInputFilter = m::mock();
        $mockDataFilter = m::mock();

        // Mocked services
        $mockLicenceEntityService = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntityService);
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockLicenceAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceAdapter);
        $mockValidator = m::mock();
        $this->sm->setService('postcodeTrafficAreaValidator', $mockValidator);
        $mockLocService = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLocService);
        $mockCantIncreaseValidator = m::mock();
        $this->sm->setService('CantIncreaseValidator', $mockCantIncreaseValidator);
        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        // Expectations
        $mockLicenceEntityService->shouldReceive('getTypeOfLicenceData')
            ->andReturn($stubbedTolData);

        $mockForm->shouldReceive('get')
            ->with('data')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('sufficientParking')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('permission')
            ->andReturnSelf();

        $mockFormHelper->shouldReceive('remove')
            ->with($mockForm, 'data->noOfTrailersRequired')
            ->shouldReceive('remove')
            ->with($mockForm, 'advertisements')
            ->shouldReceive('alterElementLabel')
            ->times(3)
            ->with($mockForm, '-psv', FormHelperService::ALTER_LABEL_APPEND);

        $mockLicenceAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntityService->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn($stubbedTrafficArea);

        $mockLocService->shouldReceive('getOperatingCentresCount')
            ->andReturn(['Count' => 1]);

        $mockValidator->shouldReceive('setNiFlag')
            ->with('N')
            ->shouldReceive('setOperatingCentresCount')
            ->with(1)
            ->shouldReceive('setTrafficArea')
            ->with($stubbedTrafficArea);

        $mockInputFilter->shouldReceive('get')
            ->with('address')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('postcode')
            ->andReturnSelf()
            ->shouldReceive('setRequired')
            ->with(false)
            ->andReturnSelf()
            ->shouldReceive('getValidatorChain')
            ->andReturn(
                m::mock()
                ->shouldReceive('attach')
                ->with($mockValidator)
                ->getMock()
            );

        $mockForm->shouldReceive('getInputFilter')
            ->andReturn($mockInputFilter);

        $mockInputFilter->shouldReceive('get')
            ->with('data')
            ->andReturn(
                m::mock()
                ->shouldReceive('has')
                ->andReturn(true)
                ->shouldReceive('get')
                ->with('noOfVehiclesRequired')
                ->andReturn($mockDataFilter)
                ->shouldReceive('get')
                ->with('noOfTrailersRequired')
                ->andReturn($mockDataFilter)
                ->getMock()
            );

        $this->controller->shouldReceive('params')
            ->with('child_id')
            ->andReturn($childId);

        $mockLocService->shouldReceive('getVehicleAuths')
            ->with($childId)
            ->andReturn($stubbedVehicleAuths);

        $this->controller->shouldReceive('url->fromRoute')
            ->with('create_variation', ['licence' => $licenceId])
            ->andReturn('URL');

        $mockTranslator->shouldReceive('translateReplace')
            ->with('cant-increase-vehicles', ['URL'])
            ->andReturn('MESSAGE 1')
            ->shouldReceive('translateReplace')
            ->with('cant-increase-trailers', ['URL'])
            ->andReturn('MESSAGE 2');

        $mockCantIncreaseValidator->shouldReceive('setGenericMessage')
            ->with('MESSAGE 1')
            ->shouldReceive('setGenericMessage')
            ->with('MESSAGE 2')
            ->shouldReceive('setPreviousValue')
            ->with(123)
            ->shouldReceive('setPreviousValue')
            ->with(456);

        $mockDataFilter->shouldReceive('getValidatorChain')
            ->andReturn(
                m::mock()
                ->shouldReceive('attach')
                ->with($mockCantIncreaseValidator)
                ->getMock()
            );

        $this->assertSame($mockForm, $this->sut->alterActionForm($mockForm));
    }

    public function testDisableConditionalValidationWithoutDataSet()
    {
        // Stubbed data
        $post = [];
        $licenceId = 3;
        $stubbedTotalAuth = [];

        // Mock dependencies
        $mockForm = m::mock('\Zend\Form\Form');
        $mockInputFilter = m::mock();

        // Mock services
        $mockLicenceAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceAdapter);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Set expectations
        $this->controller->shouldReceive('getRequest->getPost')
            ->andReturn($post);

        $mockLicenceAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntity->shouldReceive('getTotalAuths')
            ->with($licenceId)
            ->andReturn($stubbedTotalAuth);

        $mockInputFilter->shouldReceive('get')
            ->with('data')
            ->andReturnSelf();

        $mockForm->shouldReceive('getInputFilter')
            ->andReturn($mockInputFilter);

        $this->sut->disableConditionalValidation($mockForm);
    }

    public function testDisableConditionalValidation()
    {
        // Stubbed data
        $post = [
            'data' => [
                'totAuthLargeVehicles' => 1,
                'totAuthMediumVehicles' => 2,
                'totAuthSmallVehicles' => 3,
                'totAuthVehicles' => 4,
                'totAuthTrailers' => 5
            ]
        ];
        $licenceId = 3;
        $stubbedTotalAuth = [
            'totAuthLargeVehicles' => 1,
            'totAuthMediumVehicles' => 2,
            'totAuthSmallVehicles' => 3,
            'totAuthVehicles' => 4,
            'totAuthTrailers' => 5
        ];

        // Mock dependencies
        $mockForm = m::mock('\Zend\Form\Form');
        $mockInputFilter = m::mock();
        $mockInput = m::mock();

        // Mock services
        $mockLicenceAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceAdapter);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        // Set expectations
        $this->controller->shouldReceive('getRequest->getPost')
            ->andReturn($post);

        $mockLicenceAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntity->shouldReceive('getTotalAuths')
            ->with($licenceId)
            ->andReturn($stubbedTotalAuth);

        $mockInputFilter->shouldReceive('get')
            ->with('data')
            ->andReturnSelf();

        $mockForm->shouldReceive('getInputFilter')
            ->andReturn($mockInputFilter);

        $mockInputFilter->shouldReceive('get')
            ->times(5)
            ->andReturn($mockInput);

        $mockFormHelper->shouldReceive('disableValidation')
            ->times(5)
            ->with($mockInput);

        $this->sut->disableConditionalValidation($mockForm);
    }

    public function testAlterForm()
    {
        // Stubbed data
        $licenceId = 5;
        $stubbedAddressData = [
            'Results' => []
        ];
        $stubbedTolData = [
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
        ];

        // Mocked objects
        $mockForm = m::mock('\Zend\Form\Form');

        // Mocked services
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);
        $mockEntityService = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockEntityService);
        $mockLicenceAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceAdapter);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        // Expectations
        $mockLicenceAdapter->shouldReceive('alterForm')
            ->with($mockForm);

        $mockLicenceAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntity->shouldReceive('getTypeOfLicenceData')
            ->with($licenceId)
            ->andReturn($stubbedTolData);

        $this->fail('Finish me off');

        /*

        $mockEntityService->shouldReceive('getAddressSummaryData')
            ->with($licenceId)
            ->andReturn($stubbedAddressData);*/

        $this->sut->alterForm($mockForm);
    }
}
