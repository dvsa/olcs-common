<?php

/**
 * Licence Type Of Licence Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Controller\Lva\Adapters\LicenceTypeOfLicenceAdapter;
use CommonTest\Bootstrap;
use Common\Service\Entity\LicenceEntityService;

/**
 * Licence Type Of Licence Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTypeOfLicenceAdapterTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected function setUp()
    {
        $this->sut = new LicenceTypeOfLicenceAdapter();

        $this->sm = Bootstrap::getServiceManager();
        $this->controller = m::mock('\Zend\Mvc\Controller\AbstractController');

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    /**
     * @group licence_type_of_licence_adapter
     */
    public function testDoesChangeRequireConfirmation()
    {
        $postData = array('licence-type' => 'A');

        $currentData = array('licenceType' => 'B');

        $this->assertTrue($this->sut->doesChangeRequireConfirmation($postData, $currentData));
    }

    /**
     * @group licence_type_of_licence_adapter
     */
    public function testDoesChangeRequireConfirmationFalse()
    {
        $postData = array('licence-type' => 'B');

        $currentData = array('licenceType' => 'B');

        $this->assertFalse($this->sut->doesChangeRequireConfirmation($postData, $currentData));
    }

    /**
     * @group licence_type_of_licence_adapter
     * @dataProvider providerShouldDisableLicenceType
     */
    public function testShouldDisableLicenceType($id, $applicationType, $stubbedData, $expected)
    {
        $mockAppEntityService = m::mock();
        $mockAppEntityService->shouldReceive('getTypeOfLicenceData')
            ->with($id)
            ->andReturn($stubbedData);

        $this->sm->setService('Entity\Licence', $mockAppEntityService);

        $this->assertEquals($expected, $this->sut->shouldDisableLicenceType($id, $applicationType));
    }

    /**
     * @group licence_type_of_licence_adapter
     */
    public function testAlterForm()
    {
        $id = 4;
        $applicationType = 'internal';
        $stubbedData = [
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
        ];

        $mockAppEntityService = m::mock();
        $mockAppEntityService->shouldReceive('getTypeOfLicenceData')
            ->with($id)
            ->andReturn($stubbedData);

        $form = m::mock('\Zend\Form\Form');
        $mockFormHelper = m::mock();
        $tolFieldset = m::mock();

        $operatorLocation = m::mock();
        $operatorLocation->shouldReceive('setLabel')
            ->with('operator-location');

        $operatorType = m::mock();
        $operatorType->shouldReceive('setLabel')
            ->with('operator-type');

        $tolFieldset->shouldReceive('get')
            ->with('operator-location')
            ->andReturn($operatorLocation)
            ->shouldReceive('get')
            ->with('operator-type')
            ->andReturn($operatorType)
            ->shouldReceive('get')
            ->with('licence-type')
            ->andReturn(
                m::mock()->shouldReceive('setLabel')
                ->with('licence-type')->getMock()
            );

        $form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()->shouldReceive('get')
                ->with('save')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setLabel')
                    ->with('save')->getMock()
                )->getMock()
            )
            ->shouldReceive('get')
            ->with('type-of-licence')
            ->andReturn($tolFieldset);

        $mockFormHelper->shouldReceive('lockElement')
            ->with($operatorLocation, 'operator-location-lock-message')
            ->shouldReceive('lockElement')
            ->with($operatorType, 'operator-type-lock-message')
            ->shouldReceive('disableElement')
            ->with($form, 'type-of-licence->operator-location')
            ->shouldReceive('disableElement')
            ->with($form, 'type-of-licence->operator-type');

        $this->sm->setService('Helper\Form', $mockFormHelper);
        $this->sm->setService('Entity\Licence', $mockAppEntityService);

        $this->assertSame($form, $this->sut->alterForm($form, $id, $applicationType));
    }

    /**
     * @group licence_type_of_licence_adapter
     */
    public function testAlterFormWithDisabledTol()
    {
        $id = 4;
        $applicationType = 'external';
        $stubbedData = [
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV,
            'licenceType' => LicenceEntityService::LICENCE_TYPE_RESTRICTED
        ];

        $mockAppEntityService = m::mock();
        $mockAppEntityService->shouldReceive('getTypeOfLicenceData')
            ->with($id)
            ->andReturn($stubbedData);

        $form = m::mock('\Zend\Form\Form');
        $mockFormHelper = m::mock();
        $tolFieldset = m::mock();

        $operatorLocation = m::mock();
        $operatorLocation->shouldReceive('setLabel')
            ->with('operator-location');

        $operatorType = m::mock();
        $operatorType->shouldReceive('setLabel')
            ->with('operator-type');

        $licenceType = m::mock();
        $licenceType->shouldReceive('setLabel')
            ->with('licence-type')->getMock();

        $tolFieldset->shouldReceive('get')
            ->with('operator-location')
            ->andReturn($operatorLocation)
            ->shouldReceive('get')
            ->with('operator-type')
            ->andReturn($operatorType)
            ->shouldReceive('get')
            ->with('licence-type')
            ->andReturn($licenceType);

        $form->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()->shouldReceive('get')
                ->with('save')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setLabel')
                    ->with('save')->getMock()
                )->getMock()
            )
            ->shouldReceive('get')
            ->with('type-of-licence')
            ->andReturn($tolFieldset);

        $mockFormHelper->shouldReceive('lockElement')
            ->with($operatorLocation, 'operator-location-lock-message')
            ->shouldReceive('lockElement')
            ->with($operatorType, 'operator-type-lock-message')
            ->shouldReceive('disableElement')
            ->with($form, 'type-of-licence->operator-location')
            ->shouldReceive('disableElement')
            ->with($form, 'type-of-licence->operator-type')
            ->shouldReceive('disableElement')
            ->with($form, 'type-of-licence->licence-type')
            ->shouldReceive('lockElement')
            ->with($licenceType, 'licence-type-lock-message')
            ->shouldReceive('disableElement')
            ->with($form, 'form-actions->save');

        $this->sm->setService('Helper\Form', $mockFormHelper);
        $this->sm->setService('Entity\Licence', $mockAppEntityService);

        $this->assertSame($form, $this->sut->alterForm($form, $id, $applicationType));
    }

    /**
     * @group licence_type_of_licence_adapter
     */
    public function testSetMessages()
    {
        $id = 4;
        $applicationType = 'external';
        $stubbedData = [
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV,
            'licenceType' => LicenceEntityService::LICENCE_TYPE_RESTRICTED
        ];

        $mockFlashMessenger = m::mock();
        $mockFlashMessenger->shouldReceive('addInfoMessage')
            ->with('variation-application-text3');

        $mockAppEntityService = m::mock();
        $mockAppEntityService->shouldReceive('getTypeOfLicenceData')
            ->with($id)
            ->andReturn($stubbedData);

        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);
        $this->sm->setService('Entity\Licence', $mockAppEntityService);

        $this->sut->setMessages($id, $applicationType);
    }

    /**
     * @group licence_type_of_licence_adapter
     */
    public function testSetMessagesWithoutDisable()
    {
        $id = 4;
        $applicationType = 'internal';
        $stubbedData = [
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV,
            'licenceType' => LicenceEntityService::LICENCE_TYPE_RESTRICTED
        ];

        $mockFlashMessenger = m::mock();
        $mockFlashMessenger->shouldReceive('addInfoMessage')
            ->with('MESSAGE');

        $mockAppEntityService = m::mock();
        $mockAppEntityService->shouldReceive('getTypeOfLicenceData')
            ->with($id)
            ->andReturn($stubbedData);

        $expectedTranslationVariables = [
            'variation-application-text2',
            // @todo replace with real link
            'https://www.google.co.uk/?q=Licence+Type#q=Licence+Type',
            'variation-application-link-text'
        ];

        $mockTranslationHelper = m::mock();
        $mockTranslationHelper->shouldReceive('formatTranslation')
            ->with('%s <a href="%s" target="_blank">%s</a>', $expectedTranslationVariables)
            ->andReturn('MESSAGE');

        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);
        $this->sm->setService('Entity\Licence', $mockAppEntityService);
        $this->sm->setService('Helper\Translation', $mockTranslationHelper);

        $this->sut->setMessages($id, $applicationType);
    }

    /**
     * @group licence_type_of_licence_adapter
     */
    public function testConfirmationActionWithGet()
    {
        $mockRequest = m::mock();
        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $this->controller->shouldReceive('getRequest')
            ->andReturn($mockRequest);

        $form = m::mock();
        $form->shouldReceive('get->get->setLabel')
            ->with('create-variation-button');

        $mockFormHelper = m::mock();
        $mockFormHelper->shouldReceive('createForm')
            ->with('GenericConfirmation')
            ->andReturn($form)
            ->shouldReceive('setFormActionFromRequest')
            ->with($form, $mockRequest);

        $this->sm->setService('Helper\Form', $mockFormHelper);

        $this->assertSame($form, $this->sut->confirmationAction());
    }

    /**
     * @group licence_type_of_licence_adapter
     */
    public function testConfirmationActionWithPost()
    {
        $mockRequest = m::mock();
        $mockRequest->shouldReceive('isPost')
            ->andReturn(true);

        $this->controller->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with()
            ->andReturnSelf()
            ->shouldReceive('fromQuery')
            ->with('licence-type')
            ->andReturn('xxx')
            ->shouldReceive('params')
            ->with('licence')
            ->andReturn(3);

        $this->controller->shouldReceive('redirect->toRouteAjax')
            ->with('lva-variation', ['application' => 5])
            ->andReturn('REDIRECT');

        $mockAppService = m::mock();
        $mockAppService->shouldReceive('createVariation')
            ->with(3, ['licenceType' => 'xxx'])
            ->andReturn(5);

        $this->sm->setService('Entity\Application', $mockAppService);


        $this->assertEquals('REDIRECT', $this->sut->confirmationAction());
    }

    public function providerShouldDisableLicenceType()
    {
        return [
            [
                1,
                'internal',
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                ],
                false
            ],
            [
                1,
                'internal',
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV,
                    'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
                ],
                false
            ],
            [
                1,
                'internal',
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV,
                    'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                ],
                false
            ],
            [
                1,
                'internal',
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV,
                    'licenceType' => LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
                ],
                true
            ],
            [
                1,
                'external',
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV,
                    'licenceType' => LicenceEntityService::LICENCE_TYPE_RESTRICTED
                ],
                true
            ],
            [
                1,
                'internal',
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV,
                    'licenceType' => LicenceEntityService::LICENCE_TYPE_RESTRICTED
                ],
                false
            ]
        ];
    }
}
