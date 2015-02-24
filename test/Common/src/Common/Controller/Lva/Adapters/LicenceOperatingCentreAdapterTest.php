<?php

/**
 * Licence Operating Centre Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use MUnit\Adapter\Mockery\TestCase;
use Common\Controller\Lva\Adapters\LicenceOperatingCentreAdapter;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Helper\FormHelperService;

/**
 * Licence Operating Centre Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentreAdapterTest extends TestCase
{
    protected $sut;
    protected $sm;
    protected $controller;

    /**
     * Renamed this method as we are using MUnit for some methods in this file
     */
    public function setUpTest()
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
        $this->setUpTest();

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
        $this->setUpTest();

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

        $mockFlashMessenger->shouldReceive('addCurrentInfoMessage')
            ->with('TRANSLATED');

        $this->sut->addMessages();
    }

    public function testAlterActionFormForGoodsWithoutVehicleOrTrailerElement()
    {
        $this->setUpTest();

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
        $this->setUpTest();

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
        $this->setUpTest();

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
        $this->setUpTest();

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

    /**
     * @group POC
     */
    public function testAlterForm()
    {
        // Licence ID:5
        $this->describe('alterForm', array($this, 'describeAlterForm'));
    }

    public function describeAlterForm()
    {
        $this->beforeEach(
            function ($scope) {
                $this->setUpTest();

                // Stubbed data
                $stubbedTotalAuths = [
                    'totAuthVehicles' => 10
                ];

                // Mocked services
                $scope->mockFormHelper = m::mock();
                $this->sm->setService('Helper\Form', $scope->mockFormHelper);
                $scope->mockLicenceAdapter = m::mock();
                $this->sm->setService('LicenceLvaAdapter', $scope->mockLicenceAdapter);
                $scope->mockEntityService = m::mock();
                $this->sm->setService('Entity\LicenceOperatingCentre', $scope->mockEntityService);
                $scope->mockLicenceEntity = m::mock();
                $this->sm->setService('Entity\Licence', $scope->mockLicenceEntity);
                $scope->mockValidator = m::mock();
                $this->sm->setService('CantIncreaseValidator', $scope->mockValidator);
                $scope->mockTranslator = m::mock();
                $this->sm->setService('Helper\Translation', $scope->mockTranslator);

                // Mocked objects
                $scope->mockForm = m::mock('\Zend\Form\Form');
                $scope->mockInputFilter = m::mock();
                $scope->mockDataInputFilter = m::mock();
                $scope->mockValidatorChain = m::mock();
                $scope->mockDataElement = m::mock();
                $scope->mockCommunityFilter = m::mock();

                // Expectations
                $scope->mockLicenceAdapter->shouldReceive('setController')
                    ->with($this->controller)
                    ->shouldReceive('getIdentifier')
                    ->andReturn(5);

                $scope->mockLicenceEntity->shouldReceive('getTotalAuths')
                    ->with(5)
                    ->andReturn($stubbedTotalAuths);

                $scope->mockForm->shouldReceive('getInputFilter')
                    ->andReturn($scope->mockInputFilter);

                $scope->mockDataInputFilter->shouldReceive('has')
                    ->with('totAuthVehicles')
                    ->andReturn(true)
                    ->shouldReceive('has')
                    ->with('totAuthTrailers')
                    ->andReturn(false)
                    ->shouldReceive('get')
                    ->with('totAuthVehicles')
                    ->andReturnSelf()
                    ->shouldReceive('getValidatorChain')
                    ->andReturn($scope->mockValidatorChain)
                    ->shouldReceive('get')
                    ->with('totCommunityLicences')
                    ->andReturn($scope->mockCommunityFilter);

                $scope->mockInputFilter->shouldReceive('get')
                    ->with('data')
                    ->andReturn($scope->mockDataInputFilter);

                $this->controller->shouldReceive('url->fromRoute')
                    ->with('create_variation', ['licence' => 5])
                    ->andReturn('LINK');

                $scope->mockTranslator->shouldReceive('translateReplace')
                    ->with('cant-increase-total-vehicles', ['LINK'])
                    ->andReturn('MESSAGE');

                $scope->mockValidator->shouldReceive('setGenericMessage')
                    ->with('MESSAGE')
                    ->shouldReceive('setPreviousValue')
                    ->with(10);

                $scope->mockValidatorChain->shouldReceive('attach')
                    ->with($scope->mockValidator);

                $scope->mockForm->shouldReceive('get')
                    ->with('data')
                    ->andReturn($scope->mockDataElement);

                $scope->mockDataElement->shouldReceive('has')
                    ->with('totCommunityLicences')
                    ->andReturn(true);

                $scope->mockFormHelper->shouldReceive('disableElement')
                    ->with($scope->mockForm, 'data->totCommunityLicences');

                $scope->mockFormHelper->shouldReceive('disableValidation')
                    ->with($scope->mockCommunityFilter);
            }
        );

        $this->describe('will call parent::alterForm', array($this, 'describeParentAlterForm'));
    }

    public function describeParentAlterForm()
    {
        $this->beforeEach(
            function ($scope) {
                $scope->mockLicenceAdapter->shouldReceive('alterForm')->with($scope->mockForm);
                $scope->stubbedTolData = [];
            }
        );

        $this->describe('without table data', array($this, 'describeWithoutTableData'));
    }

    public function describeWithoutTableData()
    {
        $this->beforeEach(
            function ($scope) {
                $scope->mockEntityService->shouldReceive('getAddressSummaryData')
                    ->with(5)
                    ->andReturn(['Results' => []]);

                $scope->mockFormHelper->shouldReceive('remove')
                    ->with($scope->mockForm, 'dataTrafficArea');
            }
        );

        $this->describe('for a goods licence', array($this, 'describeForGoods'));

        $this->describe('for a psv licence', array($this, 'describeForPsv'));
    }

    public function describeForPsv()
    {
        $this->beforeEach(
            function ($scope) {
                $scope->mockTable = m::mock();

                $scope->stubbedTolData['goodsOrPsv'] = LicenceEntityService::LICENCE_CATEGORY_PSV;

                $scope->mockDataElement->shouldReceive('getOptions')
                    ->andReturn(['hint' => 'foo'])
                    ->shouldReceive('setOptions')
                    ->with(['hint' => 'foo.psv']);

                $scope->mockTable->shouldReceive('removeColumn')
                    ->with('noOfTrailersRequired')
                    ->shouldReceive('getFooter')
                    ->andReturn(['total' => ['content' => 'foo'], 'trailersCol' => 'bar'])
                    ->shouldReceive('setFooter')
                    ->with(['total' => ['content' => 'foo-psv']]);

                $scope->mockForm->shouldReceive('get')
                    ->with('table')
                    ->andReturnSelf()
                    ->shouldReceive('getTable')
                    ->andReturn($scope->mockTable);
            }
        );

        $this->describe(
            'for a standard national licence',
            function () {
                $this->beforeEach(
                    function ($scope) {
                        $scope->stubbedTolData['licenceType']
                            = LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL;

                        $scope->mockLicenceEntity->shouldReceive('getTypeOfLicenceData')
                            ->with(5)->andReturn($scope->stubbedTolData);

                        $expectedRemoveList = [
                            'totAuthTrailers',
                            'minTrailerAuth',
                            'maxTrailerAuth',
                            'totCommunityLicences'
                        ];

                        $scope->mockFormHelper->shouldReceive('removeFieldList')
                            ->with($scope->mockForm, 'data', $expectedRemoveList);
                    }
                );

                $this->it(
                    'should meet all expectations',
                    function ($scope) {
                        $this->assertSame($scope->mockForm, $this->sut->alterForm($scope->mockForm));
                    }
                );
            }
        );

        $this->describe(
            'for a standard international licence',
            function () {
                $this->beforeEach(
                    function ($scope) {
                        $scope->stubbedTolData['licenceType']
                            = LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL;

                        $scope->mockLicenceEntity->shouldReceive('getTypeOfLicenceData')
                            ->with(5)->andReturn($scope->stubbedTolData);

                        $expectedRemoveList = [
                            'totAuthTrailers',
                            'minTrailerAuth',
                            'maxTrailerAuth'
                        ];

                        $scope->mockFormHelper->shouldReceive('removeFieldList')
                            ->with($scope->mockForm, 'data', $expectedRemoveList);
                    }
                );

                $this->it(
                    'should meet all expectations',
                    function ($scope) {
                        $this->assertSame($scope->mockForm, $this->sut->alterForm($scope->mockForm));
                    }
                );
            }
        );

        $this->describe(
            'for a restricted licence',
            function () {
                $this->beforeEach(
                    function ($scope) {
                        $scope->stubbedTolData['licenceType']
                            = LicenceEntityService::LICENCE_TYPE_RESTRICTED;

                        $scope->mockLicenceEntity->shouldReceive('getTypeOfLicenceData')
                            ->with(5)->andReturn($scope->stubbedTolData);

                        $expectedRemoveList = [
                            'totAuthTrailers',
                            'minTrailerAuth',
                            'maxTrailerAuth',
                            'totAuthLargeVehicles'
                        ];

                        $scope->mockFormHelper->shouldReceive('removeFieldList')
                            ->with($scope->mockForm, 'data', $expectedRemoveList);
                    }
                );

                $this->it(
                    'should meet all expectations',
                    function ($scope) {
                        $this->assertSame($scope->mockForm, $this->sut->alterForm($scope->mockForm));
                    }
                );
            }
        );

        $this->describe(
            'for a special restricted licence',
            function () {
                $this->beforeEach(
                    function ($scope) {
                        $scope->stubbedTolData['licenceType']
                            = LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED;

                        $scope->mockLicenceEntity->shouldReceive('getTypeOfLicenceData')
                            ->with(5)->andReturn($scope->stubbedTolData);

                        $expectedRemoveList = [
                            'totAuthTrailers',
                            'minTrailerAuth',
                            'maxTrailerAuth',
                            'totAuthLargeVehicles',
                            'totCommunityLicences'
                        ];

                        $scope->mockFormHelper->shouldReceive('removeFieldList')
                            ->with($scope->mockForm, 'data', $expectedRemoveList);
                    }
                );

                $this->it(
                    'should meet all expectations',
                    function ($scope) {
                        $this->assertSame($scope->mockForm, $this->sut->alterForm($scope->mockForm));
                    }
                );
            }
        );
    }

    public function describeForGoods()
    {
        $this->beforeEach(
            function ($scope) {
                $scope->stubbedTolData['goodsOrPsv'] = LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE;
            }
        );

        $this->describe(
            'for a standard national licence',
            function () {
                $this->beforeEach(
                    function ($scope) {
                        $scope->stubbedTolData['licenceType']
                            = LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL;

                        $scope->mockLicenceEntity->shouldReceive('getTypeOfLicenceData')
                            ->with(5)->andReturn($scope->stubbedTolData);

                        $expectedRemoveList = [
                            'totAuthSmallVehicles',
                            'totAuthMediumVehicles',
                            'totAuthLargeVehicles',
                            'totCommunityLicences'
                        ];

                        $scope->mockFormHelper->shouldReceive('removeFieldList')
                            ->with($scope->mockForm, 'data', $expectedRemoveList);

                        $scope->mockFormHelper->shouldReceive('removeValidator')
                            ->with(
                                $scope->mockForm,
                                'data->totAuthVehicles',
                                'Common\Form\Elements\Validators\EqualSum'
                            );
                    }
                );

                $this->it(
                    'should meet all expectations',
                    function ($scope) {
                        $this->assertSame($scope->mockForm, $this->sut->alterForm($scope->mockForm));
                    }
                );
            }
        );

        $this->describe(
            'for a standard international licence',
            function () {
                $this->beforeEach(
                    function ($scope) {
                        $scope->stubbedTolData['licenceType']
                            = LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL;

                        $scope->mockLicenceEntity->shouldReceive('getTypeOfLicenceData')
                            ->with(5)->andReturn($scope->stubbedTolData);

                        $expectedRemoveList = [
                            'totAuthSmallVehicles',
                            'totAuthMediumVehicles',
                            'totAuthLargeVehicles'
                        ];

                        $scope->mockFormHelper->shouldReceive('removeFieldList')
                            ->with($scope->mockForm, 'data', $expectedRemoveList);

                        $scope->mockFormHelper->shouldReceive('removeValidator')
                            ->with(
                                $scope->mockForm,
                                'data->totAuthVehicles',
                                'Common\Form\Elements\Validators\EqualSum'
                            );
                    }
                );

                $this->it(
                    'should meet all expectations',
                    function ($scope) {
                        $this->assertSame($scope->mockForm, $this->sut->alterForm($scope->mockForm));
                    }
                );
            }
        );
    }

    public function testSaveMainFormData()
    {
        $this->setUpTest();

        // Stubbed data
        $data = [
            'foo' => 'bar',
            'totCommunityLicences' => 'abc'
        ];

        // Mock services
        $mockDataService = m::mock();
        $this->sm->setService('Helper\Data', $mockDataService);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        $mockDataService->shouldReceive('processDataMap')
            ->with($data, ['main' => ['mapFrom' => ['data', 'dataTrafficArea']]])
            ->andReturn($data);

        $mockLicenceEntity->shouldReceive('save')
            ->with(['foo' => 'bar']);

        $this->sut->saveMainFormData($data);
    }
}
