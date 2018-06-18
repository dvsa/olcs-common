<?php

namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\FormHelperService;
use Mockery as m;
use Zend\Form\Element\Select;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Form;

/**
* @covers \Common\Service\Helper\FormHelperService
 */
class FormHelperServiceTest extends MockeryTestCase
{
    /** @var FormHelperService  */
    private $sut;
    /** @var \Zend\ServiceManager\ServiceLocatorInterface | m\MockInterface */
    private $mockSm;
    /** @var \ZfcRbac\Service\AuthorizationService | m\MockInterface */
    private $mockAuthSrv;
    /** @var \Common\Form\Annotation\CustomAnnotationBuilder | m\MockInterface */
    private $mockBuilder;
    /** @var  \Common\Service\Helper\AddressHelperService | m\MockInterface */
    private $mockHlpAddr;
    /** @var  \Zend\View\Renderer\RendererInterface | m\MockInterface */
    private $mockRenderer;
    /** @var  \Common\Service\Helper\TranslationHelperService | m\MockInterface */
    private $mockTransSrv;
    /** @var  \Common\Service\Data\AddressDataService| m\MockInterface */
    private $mockDataAddress;
    /** @var  \Common\Service\Data\CompaniesHouseDataService | m\MockInterface */
    private $mockDataCompHouse;

    public function setUp()
    {
        $this->mockBuilder = m::mock(\Common\Form\Annotation\CustomAnnotationBuilder::class);
        $this->mockAuthSrv = m::mock(\ZfcRbac\Service\AuthorizationService::class);

        $this->mockTransSrv = m::mock(\Common\Service\Helper\TranslationHelperService::class);
        $this->mockRenderer = m::mock(\Zend\View\Renderer\RendererInterface::class);
        $this->mockHlpAddr = m::mock(\Common\Service\Helper\AddressHelperService::class);

        $this->mockDataCompHouse = m::mock(\Common\Service\Data\CompaniesHouseDataService::class);
        $this->mockDataAddress = m::mock(\Common\Service\Data\AddressDataService::class);

        $this->mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $this->mockSm
            ->shouldReceive('get')->with(\ZfcRbac\Service\AuthorizationService::class)->andReturn($this->mockAuthSrv)
            ->shouldReceive('get')->with('ViewRenderer')->andReturn($this->mockRenderer)
            ->shouldReceive('get')->with('FormAnnotationBuilder')->andReturn($this->mockBuilder)
            ->shouldReceive('get')->with('Helper\Translation')->andReturn($this->mockTransSrv)
            ->shouldReceive('get')->with('translator')->andReturn($this->mockTransSrv)
            ->shouldReceive('get')->with('Helper\Address')->andReturn($this->mockHlpAddr)
            ->shouldReceive('get')->with('Data\Address')->andReturn($this->mockDataAddress)
            ->shouldReceive('get')->with('Data\CompaniesHouse')->andReturn($this->mockDataCompHouse);

        $this->sut = new FormHelperService();
        $this->sut->setServiceLocator($this->mockSm);
    }

    public function testAlterElementLabelWithAppend()
    {
        $element = m::mock('\stdClass');
        $element->shouldReceive('getLabel')->andReturn('My label');
        $element->shouldReceive('setLabel')->with('My labelAppended label');

        $this->sut->alterElementLabel($element, 'Appended label', 1);
    }

    public function testAlterElementLabelWithNoType()
    {
        $element = m::mock('\stdClass');
        $element->shouldReceive('getLabel')->andReturn('My label');
        $element->shouldReceive('setLabel')->with('Replaced label');

        $this->sut->alterElementLabel($element, 'Replaced label');
    }

    public function testAlterElementLabelWithPrepend()
    {
        $element = m::mock('\stdClass');
        $element->shouldReceive('getLabel')->andReturn('My label');
        $element->shouldReceive('setLabel')->with('Prepended labelMy label');

        $this->sut->alterElementLabel($element, 'Prepended label', 2);
    }

    public function testCreateFormWithInvalidForm()
    {
        try {
            $this->sut->createForm('NotFound');
        } catch (\RuntimeException $ex) {
            $this->assertEquals('Form does not exist: NotFound', $ex->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }

    public function testCreateFormWithValidForm()
    {
        //  register class in namespace; do not remove mock
        $formClass = 'Common\Form\Model\Form\MyFakeFormTest';
        m::mock($formClass);

        $mockForm = m::mock(\Zend\Form\Form::class);
        $mockForm
            ->shouldReceive('add')
            ->once()
            ->with(
                array(
                    'type' => \Zend\Form\Element\Csrf::class,
                    'name' => 'security',
                    'options' => array(
                        'csrf_options' => array(
                            'messageTemplates' => array(
                                'notSame' => 'csrf-message'
                            ),
                            'timeout' => 9999,
                        )
                    ),
                    'attributes' => array(
                        'class' => 'js-csrf-token'
                    )
                )
            )
            ->shouldReceive('add')
            ->once()
            ->with(
                array(
                    'type' => \Zend\Form\Element\Button::class,
                    'name' => 'form-actions[continue]',
                    'options' => array(
                        'label' => 'Continue'
                    ),
                    'attributes' => array(
                        'type' => 'submit',
                        'class' => 'visually-hidden',
                        'style' => 'display: none;',
                        'id' => 'hidden-continue'
                    )
                )
            );

        $this->mockBuilder->shouldReceive('createForm')->once()->with($formClass)->andReturn($mockForm);
        $this->mockAuthSrv->shouldReceive('isGranted')->with('internal-user')->andReturn(false);

        $mockCfg = [
            'csrf' => [
                'timeout' => 9999,
            ]
        ];
        $this->mockSm->shouldReceive('get')->once()->with('Config')->andReturn($mockCfg);

        /** @var FormHelperService | m\MockInterface $sut */
        $sut = m::mock(FormHelperService::class)->makePartial();
        $sut->setServiceLocator($this->mockSm);

        static::assertEquals($mockForm, $sut->createForm($formClass));
    }

    public function testCreateFormWithoutCsrfAndCntn()
    {
        //  register class in namespace; do not remove mock
        $formClass = 'Common\Form\Model\Form\MyFakeFormTest';
        m::mock($formClass);

        $mockForm = m::mock(\Zend\Form\Form::class);
        $mockForm->shouldReceive('add')
            ->never()
            ->with(
                \Mockery::on(
                    function ($arg) {
                        $res = array_intersect_key($arg, ['type' => 1, 'name' => 1]);

                        $avail = [
                            [
                                'type' => \Zend\Form\Element\Csrf::class,
                                'name' => 'security',
                            ],
                            [
                                'type' => \Zend\Form\Element\Button::class,
                                'name' => 'form-actions[continue]',
                            ],
                        ];

                        foreach ($avail as $opt) {
                            if ($opt == $res) {
                                return true;
                            }
                        }

                        return false;
                    }
                )
            );

        $this->mockBuilder->shouldReceive('createForm')->once()->with($formClass)->andReturn($mockForm);
        $this->mockAuthSrv->shouldReceive('isGranted')->with('internal-user')->andReturn(false);

        $this->mockSm->shouldReceive('get')->once()->with('Config')->andReturn([]);

        /** @var FormHelperService | m\MockInterface $sut */
        $sut = m::mock(FormHelperService::class)->makePartial();
        $sut->setServiceLocator($this->mockSm);

        static::assertEquals($mockForm, $sut->createForm($formClass, false, false));
    }

    public function testProcessAddressLookupWithNoPostcodeOrAddressSelected()
    {
        $form = m::mock('Zend\Form\Form');

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('getPost')
            ->andReturn([])
            ->shouldReceive('isPost')
            ->andReturn(false);

        $element = m::mock('\stdClass');
        $element->shouldReceive('remove')
            ->with('addresses')
            ->shouldReceive('remove')
            ->with('select');

        $fieldset = m::mock('Common\Form\Elements\Types\Address');
        $fieldset->shouldReceive('getName')
            ->andReturn('address')
            ->shouldReceive('get')
            ->with('searchPostcode')
            ->andReturn($element);

        $form->shouldReceive('getFieldsets')
            ->once()
            ->andReturn([$fieldset]);

        $this->assertFalse(
            $this->sut->processAddressLookupForm($form, $request)
        );
    }

    public function testProcessAddressLookupWithAddressSelected()
    {
        $this->mockDataAddress->shouldReceive('getAddressForUprn')
            ->with(['address1'])
            ->andReturn('address_1234');

        $this->mockHlpAddr->shouldReceive('formatPostalAddress')
            ->with('address_1234')
            ->andReturn('formatted1');

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('getPost')
            ->andReturn(
                [
                    'address' => [
                        'searchPostcode' => [
                            'select' => true,
                            'addresses' => ['address1']
                        ]
                    ]
                ]
            )
            ->shouldReceive('isPost')
            ->andReturn(true);

        $element = m::mock('\stdClass');
        $element->shouldReceive('remove')
            ->with('addresses')
            ->shouldReceive('remove')
            ->with('select');

        $fieldset = m::mock('Common\Form\Elements\Types\Address');
        $fieldset->shouldReceive('getName')
            ->andReturn('address')
            ->shouldReceive('get')
            ->with('searchPostcode')
            ->andReturn($element);

        $form = m::mock('Zend\Form\Form');
        $form->shouldReceive('getFieldsets')
            ->once()
            ->andReturn([$fieldset])
            ->shouldReceive('setData')
            ->with(
                ['address' => 'formatted1']
            );

        $this->assertTrue(
            $this->sut->processAddressLookupForm($form, $request)
        );
    }

    public function testProcessNestedAddressLookupWithAddressSelected()
    {
        $this->mockDataAddress->shouldReceive('getAddressForUprn')
            ->with(['address1'])
            ->andReturn('address_1234');

        $this->mockHlpAddr->shouldReceive('formatPostalAddress')
            ->with('address_1234')
            ->andReturn('formatted1');

        /** @var \Zend\Http\Request | m\MockInterface $mockReq */
        $mockReq = m::mock(\Zend\Http\Request::class);
        $mockReq->shouldReceive('getPost')
            ->andReturn(
                [
                    'top-level' => [
                        'address' => [
                            'searchPostcode' => [
                                'select' => true,
                                'addresses' => ['address1']
                            ]
                        ],
                        'foo' => 'bar'
                    ]
                ]
            )
            ->shouldReceive('isPost')
            ->andReturn(true);

        $element = m::mock('\stdClass');
        $element->shouldReceive('remove')
            ->with('addresses')
            ->shouldReceive('remove')
            ->with('select');

        $fieldset = m::mock('Common\Form\Elements\Types\Address');
        $fieldset->shouldReceive('getName')
            ->andReturn('address')
            ->shouldReceive('get')
            ->with('searchPostcode')
            ->andReturn($element);

        $topFieldset = m::mock('Zend\Form\Fieldset');
        $topFieldset->shouldReceive('getName')
            ->andReturn('top-level')
            ->shouldReceive('getFieldsets')
            ->andReturn([$fieldset]);

        /** @var \Zend\Form\FormInterface | m\MockInterface $mockForm */
        $mockForm = m::mock(\Zend\Form\Form::class);
        $mockForm->shouldReceive('getFieldsets')
            ->once()
            ->andReturn([$topFieldset])
            ->shouldReceive('setData')
            ->with(
                [
                    'top-level' => [
                        'address' => 'formatted1',
                        'foo' => 'bar'
                    ]
                ]
            );

        $this->assertTrue(
            $this->sut->processAddressLookupForm($mockForm, $mockReq)
        );
    }

    public function testProcessAddressLookupWithPostcodeSearch()
    {
        $this->mockDataAddress->shouldReceive('getAddressesForPostcode')
            ->andReturn(['address1', 'address2']);

        $this->mockHlpAddr->shouldReceive('formatAddressesForSelect')
            ->with(['address1', 'address2'])
            ->andReturn(['formatted1', 'formatted2']);

        $form = m::mock('Zend\Form\Form');

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('getPost')
            ->andReturn(
                [
                    'address' => [
                        'searchPostcode' => [
                            'search' => true,
                            'postcode' => 'LSX XXX'
                        ]
                    ]
                ]
            )
            ->shouldReceive('isPost')
            ->andReturn(true);

        $addressElement = m::mock('\stdClass');
        $addressElement->shouldReceive('setValueOptions')
            ->with(['formatted1', 'formatted2']);

        $element = m::mock('\stdClass');
        $element->shouldReceive('get')
            ->with('addresses')
            ->andReturn($addressElement);

        $fieldset = m::mock('Common\Form\Elements\Types\Address');
        $fieldset->shouldReceive('getName')
            ->andReturn('address')
            ->shouldReceive('get')
            ->with('searchPostcode')
            ->andReturn($element);

        $form->shouldReceive('getFieldsets')
            ->once()
            ->andReturn([$fieldset]);

        $this->assertTrue(
            $this->sut->processAddressLookupForm($form, $request)
        );
    }

    public function testProcessNestedAddressLookupWithPostcodeSearch()
    {
        $this->mockDataAddress->shouldReceive('getAddressesForPostcode')
            ->andReturn(['address1', 'address2']);

        $this->mockHlpAddr->shouldReceive('formatAddressesForSelect')
            ->with(['address1', 'address2'])
            ->andReturn(['formatted1', 'formatted2']);

        $form = m::mock('Zend\Form\Form');

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('getPost')
            ->andReturn(
                [
                    'deeply' => [
                        'nested' => [
                            'address' => [
                                'searchPostcode' => [
                                    'search' => true,
                                    'postcode' => 'LSX XXX'
                                ]
                            ],
                            'foo' => 'bar'
                        ],
                        'baz' => true
                    ],
                    'test' => false
                ]
            )
            ->shouldReceive('isPost')
            ->andReturn(true);

        $addressElement = m::mock('\stdClass');
        $addressElement->shouldReceive('setValueOptions')
            ->with(['formatted1', 'formatted2']);

        $element = m::mock('\stdClass');
        $element->shouldReceive('get')
            ->with('addresses')
            ->andReturn($addressElement);

        $fieldset = m::mock('Common\Form\Elements\Types\Address');
        $fieldset->shouldReceive('getName')
            ->andReturn('address')
            ->shouldReceive('get')
            ->with('searchPostcode')
            ->andReturn($element);

        $topFieldset = m::mock('Zend\Form\Fieldset');
        $topFieldset->shouldReceive('getName')
            ->andReturn('deeply')
            ->shouldReceive('getFieldsets')
            ->andReturn(
                [
                    m::mock('Zend\Form\Fieldset')
                    ->shouldReceive('getName')
                    ->andReturn('nested')
                    ->shouldReceive('getFieldsets')
                    ->andReturn([$fieldset])
                    ->getMock()
                ]
            );

        $form->shouldReceive('getFieldsets')
            ->once()
            ->andReturn([$topFieldset]);

        $this->assertTrue(
            $this->sut->processAddressLookupForm($form, $request)
        );
    }

    public function testProcessAddressLookupWithEmptyAddresses()
    {
        $this->mockDataAddress->shouldReceive('getAddressesForPostcode')
            ->andReturn([]);

        $form = m::mock('Zend\Form\Form');

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('getPost')
            ->andReturn(
                [
                    'address' => [
                        'searchPostcode' => [
                            'search' => true,
                            'postcode' => 'LSX XXX'
                        ]
                    ]
                ]
            )
            ->shouldReceive('isPost')
            ->andReturn(true);

        $addressElement = m::mock('\stdClass');
        $addressElement->shouldReceive('setValueOptions')
            ->with(['formatted1', 'formatted2']);

        $element = m::mock('\stdClass');
        $element->shouldReceive('remove')
            ->with('addresses')
            ->getMock()
            ->shouldReceive('remove')
            ->with('select')
            ->getMock()
            ->shouldReceive('setMessages')
            ->with(array('postcode.error.no-addresses-found'));

        $fieldset = m::mock('Common\Form\Elements\Types\Address');
        $fieldset->shouldReceive('getName')
            ->andReturn('address')
            ->shouldReceive('get')
            ->with('searchPostcode')
            ->andReturn($element);

        $form->shouldReceive('getFieldsets')
            ->once()
            ->andReturn([$fieldset]);

        $this->assertTrue(
            $this->sut->processAddressLookupForm($form, $request)
        );
    }

    public function testProcessAddressLookupWithEmptyPostcodeSearch()
    {
        $form = m::mock('Zend\Form\Form');

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('getPost')
            ->andReturn(
                [
                    'address' => [
                        'searchPostcode' => [
                            'search' => true,
                            'postcode' => ''
                        ]
                    ]
                ]
            )
            ->shouldReceive('isPost')
            ->andReturn(true);

        $element = m::mock('\stdClass');
        $element->shouldReceive('remove')
            ->with('addresses')
            ->getMock()
            ->shouldReceive('remove')
            ->with('select')
            ->getMock()
            ->shouldReceive('setMessages')
            ->with(array('Please enter a postcode'));

        $fieldset = m::mock('Common\Form\Elements\Types\Address');
        $fieldset->shouldReceive('getName')
            ->andReturn('address')
            ->shouldReceive('get')
            ->with('searchPostcode')
            ->andReturn($element);

        $form->shouldReceive('getFieldsets')
            ->once()
            ->andReturn([$fieldset]);

        $this->assertTrue(
            $this->sut->processAddressLookupForm($form, $request)
        );
    }

    public function testProcessAddressLookupServiceUnavailable()
    {
        /** @var \Zend\ServiceManager\ServiceLocatorInterface | m\MockInterface $mockSm */
        $mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $mockSm->shouldReceive('get')->with('Data\Address')->andThrow(new \Exception('fail'));

        $this->sut->setServiceLocator($mockSm);

        $address = m::mock('\stdClass');
        $address->shouldReceive('getAddressesForPostcode')
            ->andReturn([]);

        $form = m::mock('Zend\Form\Form');

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('getPost')
            ->andReturn(
                [
                    'address' => [
                        'searchPostcode' => [
                            'search' => true,
                            'postcode' => 'LSX XXX'
                        ]
                    ]
                ]
            )
            ->shouldReceive('isPost')
            ->andReturn(true);

        $element = m::mock('\stdClass');
        $element->shouldReceive('remove')
            ->with('addresses')
            ->getMock()
            ->shouldReceive('remove')
            ->with('select')
            ->getMock()
            ->shouldReceive('setMessages')
            ->once()
            ->with(array('postcode.error.not-available'));

        $fieldset = m::mock('Common\Form\Elements\Types\Address');
        $fieldset->shouldReceive('getName')
            ->andReturn('address')
            ->shouldReceive('get')
            ->with('searchPostcode')
            ->andReturn($element);

        $form->shouldReceive('getFieldsets')
            ->once()
            ->andReturn([$fieldset]);

        $this->assertTrue(
            $this->sut->processAddressLookupForm($form, $request)
        );
    }

    public function testDisableElementWithNestedSelector()
    {
        $form = m::mock('Zend\Form\Form');

        $validator = m::mock('\stdClass');
        $validator->shouldReceive('setAllowEmpty')
            ->with(true)
            ->shouldReceive('setRequired')
            ->with(false);

        $filter = m::mock('Zend\InputFilter\InputFilter');
        $filter->shouldReceive('get')
            ->with('foo')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('get')
            ->with('bar')
            ->andReturn($validator);

        $element = m::mock('\stdClass');
        $element->shouldReceive('setAttribute')
            ->with('disabled', 'disabled');

        $fieldset = m::mock('Zend\Form\Fieldset');
        $fieldset->shouldReceive('get')
            ->with('bar')
            ->andReturn($element);

        $form->shouldReceive('getInputFilter')
            ->andReturn($filter)
            ->getMock()
            ->shouldReceive('get')
            ->with('foo')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('get')
            ->with('bar')
            ->andReturn($element);

        $this->sut->disableElement($form, 'foo->bar');
    }

    public function testDisableElementWithDateInput()
    {
        $validator = m::mock('\stdClass');
        $validator->shouldReceive('setAllowEmpty')
            ->with(true)
            ->shouldReceive('setRequired')
            ->with(false);

        $filter = m::mock('Zend\InputFilter\InputFilter');
        $filter->shouldReceive('get')
            ->with('bar')
            ->andReturn($validator);

        $element = m::mock('Zend\Form\Element\DateSelect');

        $subElement = m::mock('\stdClass');
        $subElement->shouldReceive('setAttribute')
            ->times(3)
            ->with('disabled', 'disabled');

        $element->shouldReceive('getDayElement')
            ->andReturn($subElement)
            ->getMock()
            ->shouldReceive('getMonthElement')
            ->andReturn($subElement)
            ->getMock()
            ->shouldReceive('getYearElement')
            ->andReturn($subElement);

        $fieldset = m::mock('Zend\Form\Fieldset');
        $fieldset->shouldReceive('get')
            ->with('bar')
            ->andReturn($element);

        $form = m::mock('Zend\Form\Form');
        $form->shouldReceive('getInputFilter')
            ->andReturn($filter)
            ->getMock()
            ->shouldReceive('get')
            ->with('bar')
            ->andReturn($element);

        $this->sut->disableElement($form, 'bar');
    }

    public function testDisableDateElement()
    {
        $element = m::mock('Zend\Form\Element\DateSelect');

        $subElement = m::mock('\stdClass');
        $subElement->shouldReceive('setAttribute')
            ->times(3)
            ->with('disabled', 'disabled');

        $element->shouldReceive('getDayElement')
            ->andReturn($subElement)
            ->getMock()
            ->shouldReceive('getMonthElement')
            ->andReturn($subElement)
            ->getMock()
            ->shouldReceive('getYearElement')
            ->andReturn($subElement);

        $this->sut->disableDateElement($element);
    }

    public function testEnableDateTimeElement()
    {
        $element = m::mock(\Zend\Form\Element\DateSelect::class);

        $subElement = m::mock('\stdClass');
        $subElement->shouldReceive('removeAttribute')
            ->times(5)
            ->with('disabled');

        $element->shouldReceive('getDayElement')
            ->andReturn($subElement)
            ->once()
            ->shouldReceive('getMonthElement')
            ->andReturn($subElement)
            ->once()
            ->shouldReceive('getYearElement')
            ->andReturn($subElement)
            ->once()
            ->shouldReceive('getHourElement')
            ->andReturn($subElement)
            ->once()
            ->shouldReceive('getMinuteElement')
            ->andReturn($subElement)
            ->once()
            ->getMock();

        $this->sut->enableDateTimeElement($element);
    }

    public function testRemove()
    {
        $form = m::mock('Zend\Form\Form');

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('remove')
            ->with('bar');

        $filter = m::mock('Zend\InputFilter\InputFilter');
        $filter->shouldReceive('get')
            ->with('foo')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('remove')
            ->with('bar');

        $form->shouldReceive('getInputFilter')
            ->andReturn($filter);

        $this->sut->remove($form, 'foo->bar');
    }

    public function testDisableElements()
    {
        $subElement = m::mock('\stdClass');
        $subElement->shouldReceive('setAttribute')
            ->times(3)
            ->with('disabled', 'disabled');

        $dateElement = m::mock('Zend\Form\Element\DateSelect');
        $dateElement->shouldReceive('getDayElement')
            ->andReturn($subElement)
            ->getMock()
            ->shouldReceive('getMonthElement')
            ->andReturn($subElement)
            ->getMock()
            ->shouldReceive('getYearElement')
            ->andReturn($subElement);

        $element = m::mock('Zend\Form\Element');
        $element->shouldReceive('setAttribute')
            ->with('disabled', 'disabled');

        $form = m::mock('Zend\Form\Form');

        $fieldset = m::mock('Zend\Form\Fieldset');
        $fieldset->shouldReceive('getElements')
            ->andReturn([$dateElement])
            ->getMock()
            ->shouldReceive('getFieldsets')
            ->andReturn([]);

        $form->shouldReceive('getElements')
            ->andReturn([$element])
            ->getMock()
            ->shouldReceive('getFieldsets')
            ->andReturn([$fieldset]);

        $this->sut->disableElements($form);
    }

    public function testDisableValidation()
    {
        $input = m::mock('Zend\InputFilter\Input');
        $input->shouldReceive('setAllowEmpty')
            ->with(true)
            ->getMock()
            ->shouldReceive('setRequired')
            ->with(false)
            ->getMock()
            ->shouldReceive('setValidatorChain');

        $filter = m::mock('Zend\InputFilter\InputFilter');
        $filter->shouldReceive('getInputs')
            ->andReturn([$input]);

        $this->sut->disableValidation($filter);
    }

    public function testDisableEmptyValidation()
    {
        $input = m::mock('Zend\InputFilter\Input');
        $input->shouldReceive('setAllowEmpty')
            ->with(true)
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('setRequired')
            ->with(false)
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('setValidatorChain');

        $filter = m::mock('Zend\InputFilter\InputFilter');
        $filter->shouldReceive('get')
            ->with('foo')
            ->andReturn($input)
            ->getMock()
            ->shouldReceive('has')
            ->andReturn(true)
            ->once()
            ->shouldReceive('get')
            ->with('fieldset')
            ->andReturnSelf();

        $element = m::mock('\stdClass');
        $element->shouldReceive('getValue')
            ->andReturn('');

        $fieldset = m::mock('Zend\Form\Fieldset');
        $fieldset->shouldReceive('getName')
            ->andReturn('fieldset')
            ->getMock()
            ->shouldReceive('getFieldsets')
            ->andReturn([])
            ->getMock()
            ->shouldReceive('getElements')
            ->andReturn([]);

        $form = m::mock('Zend\Form\Form');
        $form->shouldReceive('getInputFilter')
            ->andReturn($filter)
            ->getMock()
            ->shouldReceive('getElements')
            ->andReturn(['foo' => $element])
            ->getMock()
            ->shouldReceive('getFieldsets')
            ->andReturn([$fieldset]);

        $this->sut->disableEmptyValidation($form);
    }

    public function testDisableEmptyValidationOnElement()
    {
        $input = m::mock('Zend\InputFilter\Input');
        $input->shouldReceive('setAllowEmpty')
            ->with(true)
            ->andReturnSelf()
            ->shouldReceive('setRequired')
            ->with(false)
            ->andReturnSelf()
            ->shouldReceive('setValidatorChain');

        $filter = m::mock('Zend\InputFilter\InputFilter');
        $filter->shouldReceive('get')
            ->with('foo')
            ->andReturn($input)
            ->shouldReceive('get')
            ->with('fieldset')
            ->andReturnSelf();

        $element = m::mock('\stdClass');

        $fieldset = m::mock('Zend\Form\Fieldset');
        $fieldset
            ->shouldReceive('get')
            ->with('foo')
            ->andReturn($element);

        $form = m::mock('Zend\Form\Form');
        $form->shouldReceive('getInputFilter')
            ->andReturn($filter)
            ->shouldReceive('get')
            ->with('fieldset')
            ->andReturn($fieldset);

        $this->sut->disableEmptyValidationOnElement($form, 'fieldset->foo');
    }

    public function testPopulateFormTable()
    {
        $table = m::mock('Common\Service\Table\TableBuilder');
        $table->shouldReceive('getRows')
            ->andReturn([1, 2, 3, 4]);

        $tableInput = m::mock('\stdClass');
        $tableInput->shouldReceive('setTable')
            ->with($table, 'fieldset');

        $rowInput = m::mock('\stdClass');
        $rowInput->shouldReceive('setValue')
            ->with(4);

        $fieldset = m::mock('Zend\Form\Fieldset');
        $fieldset->shouldReceive('get')
            ->with('table')
            ->andReturn($tableInput)
            ->getMock()
            ->shouldReceive('get')
            ->with('rows')
            ->andReturn($rowInput);

        $this->sut->populateFormTable($fieldset, $table, 'fieldset');
    }

    public function testLockElement()
    {
        $this->mockTransSrv
            ->shouldReceive('translate')->with('message')->andReturn('translated')
            ->shouldReceive('translate')->with('label')->andReturn('label');

        $this->mockRenderer->shouldReceive('render')
            ->andReturn('template');

        $element = m::mock('Zend\Form\Element');
        $element->shouldReceive('getLabel')
            ->andReturn('label')
            ->getMock()
            ->shouldReceive('setLabel')
            ->with('labeltemplate')
            ->getMock()
            ->shouldReceive('setLabelOption')
            ->with('disable_html_escape', true)
            ->getMock()
            ->shouldReceive('getLabelAttributes')
            ->andReturn(['foo' => 'bar'])
            ->getMock()
            ->shouldReceive('setLabelAttributes')
            ->with(
                [
                    'foo' => 'bar',
                    'class' => ''
                ]
            );

        $this->sut->lockElement($element, 'message');
    }

    public function testRemoveFieldLiset()
    {
        $form = m::mock('Zend\Form\Form');

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('remove')
            ->with('bar');

        $filter = m::mock('Zend\InputFilter\InputFilter');
        $filter->shouldReceive('get')
            ->with('foo')
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('remove')
            ->with('bar');

        $form->shouldReceive('getInputFilter')
            ->andReturn($filter);

        $this->sut->removeFieldList($form, 'foo', ['bar']);
    }

    public function testProcessCompanyLookupValidData()
    {
        $this->mockDataCompHouse
            ->shouldReceive('search')
            ->with('companyDetails', '12345678')
            ->andReturn(
                [
                    'Count' => 1,
                    'Results' => [
                        [
                            'CompanyName' => 'Looked Up Company',
                            'RegAddress' => [
                                'AddressLine' => [
                                    'MILLENNIUM STADIUM',
                                    'WESTGATE STREET',
                                    'CARDIFF',
                                    'CF10 1NS',
                                ],
                            ],
                        ],
                    ],
                ]
            )
            ->getMock();

        $form = m::mock('Zend\Form\Form');

        $data = [
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ]
            ]
        ];

        $nameElement = m::mock()->shouldReceive('setValue')
            ->with('Looked Up Company')
            ->getMock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('name')
            ->andReturn($nameElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $addressFieldset = m::mock()
            ->shouldReceive('get')
            ->with('postcode')
            ->andReturn(
                m::mock()->shouldReceive('setValue')->with('CF10 1NS')->getMock()
            )
            ->shouldReceive('get')
            ->with('addressLine1')
            ->andReturn(
                m::mock()->shouldReceive('setValue')->with('MILLENNIUM STADIUM')->getMock()
            )
            ->shouldReceive('get')
            ->with('addressLine2')
            ->andReturn(
                m::mock()->shouldReceive('setValue')->with('WESTGATE STREET')->getMock()
            )
            ->shouldReceive('get')
            ->with('addressLine3')
            ->andReturn(
                m::mock()->shouldReceive('setValue')->with('CARDIFF')->getMock()
            )
            ->shouldReceive('get')
            ->with('addressLine4')
            ->andReturn(
                m::mock()->shouldReceive('setValue')->with('')->getMock()
            )
            ->shouldReceive('get')
            ->with('town')
            ->andReturn(
                m::mock()->shouldReceive('setValue')->with('')->getMock()
            )
            ->getMock();

        $form->shouldReceive('get')
            ->with('registeredAddress')
            ->andReturn($addressFieldset);

        $this->sut->processCompanyNumberLookupForm($form, $data, 'data', 'registeredAddress');
    }

    /**
     * @dataProvider companyNumberProvider
     */
    public function testProcessCompanyLookupWithNoResults($firstNumber, $secondNumber)
    {
        $service = m::mock()
            ->shouldReceive('search')
            ->with('companyDetails', $firstNumber)
            ->andReturn(
                [
                    'Count' => 0,
                    'Results' => []
                ]
            )
            ->once()
            ->shouldReceive('search')
            ->with('companyDetails', $secondNumber)
            ->andReturn(
                [
                    'Count' => 0,
                    'Results' => []
                ]
            )
            ->once()
            ->getMock();

        $translator = m::mock()
            ->shouldReceive('translate')
            ->with('company_number.search_no_results.error')
            ->andReturn('No results')
            ->getMock();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Data\CompaniesHouse')
            ->andReturn($service)
            ->shouldReceive('get')
            ->with('translator')
            ->andReturn($translator)
            ->getMock();

        $this->sut->setServiceLocator($sm);

        $form = m::mock('Zend\Form\Form');

        $data = [
            'data' => [
                'companyNumber' => [
                    'company_number' => $firstNumber
                ]
            ]
        ];

        $numberElement = m::mock()->shouldReceive('setMessages')
            ->with(
                [
                    'company_number' => ['No results']
                ]
            )
            ->getMock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('companyNumber')
            ->andReturn($numberElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $this->sut->processCompanyNumberLookupForm($form, $data, 'data');
    }

    public function companyNumberProvider()
    {
        return [
            ['01234567', '1234567'],
            ['1234567', '01234567'],
        ];
    }

    public function testProcessCompanyLookupInvalidNumber()
    {
        $translator = m::mock()
            ->shouldReceive('translate')
            ->with('company_number.length.validation.error')
            ->andReturn('Bad length')
            ->getMock();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('translator')
            ->andReturn($translator)
            ->getMock();

        $this->sut->setServiceLocator($sm);

        $form = m::mock('Zend\Form\Form');

        $data = [
            'data' => [
                'companyNumber' => [
                    'company_number' => '123456789'
                ]
            ]
        ];

        $numberElement = m::mock()->shouldReceive('setMessages')
            ->with(
                [
                    'company_number' => ['Bad length']
                ]
            )
            ->getMock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('companyNumber')
            ->andReturn($numberElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $this->sut->processCompanyNumberLookupForm($form, $data, 'data');
    }

    public function testProcessCompanyLookupError()
    {
        $service = m::mock()
            ->shouldReceive('search')
            ->with('companyDetails', '12345678')
            ->andThrow(new \Exception('xml gateway error'))
            ->getMock();

        $translator = m::mock()
            ->shouldReceive('translate')
            ->with('company_number.search_error.error')
            ->andReturn('API error')
            ->getMock();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Data\CompaniesHouse')
            ->andReturn($service)
            ->shouldReceive('get')
            ->with('translator')
            ->andReturn($translator)
            ->getMock();

        $this->sut->setServiceLocator($sm);

        $form = m::mock('Zend\Form\Form');

        $data = [
            'data' => [
                'companyNumber' => [
                    'company_number' => '12345678'
                ]
            ]
        ];

        $numberElement = m::mock()->shouldReceive('setMessages')
            ->with(
                [
                    'company_number' => ['API error']
                ]
            )
            ->getMock();

        $fieldset = m::mock()->shouldReceive('get')
            ->with('companyNumber')
            ->andReturn($numberElement)
            ->getMock();

        $form->shouldReceive('get')
            ->with('data')
            ->andReturn($fieldset);

        $this->sut->processCompanyNumberLookupForm($form, $data, 'data');
    }

    public function testSetFormActionFromRequestWhenFormHasAction()
    {
        $form = m::mock()
            ->shouldReceive('hasAttribute')
            ->with('action')
            ->andReturn(true)
            ->getMock();

        $request = m::mock()
            ->shouldReceive('getUri')->never()
            ->getMock();

        $this->sut->setFormActionFromRequest($form, $request);
    }

    public function testSetFormActionFromRequest()
    {
        $form = m::mock()
            ->shouldReceive('hasAttribute')
            ->with('action')
            ->andReturn(false)
            ->shouldReceive('setAttribute')
            ->with('action', 'URI?QUERY')
            ->getMock();

        $request = m::mock();

        $request->shouldReceive('getUri->getPath')
            ->andReturn('URI');

        $request->shouldReceive('getUri->getQuery')
            ->andReturn('QUERY');

        $this->sut->setFormActionFromRequest($form, $request);
    }

    public function testSetFormActionFromRequestWithNoQuery()
    {
        $form = m::mock()
            ->shouldReceive('getAttribute')
            ->with('method')
            ->andReturn('POST')
            ->shouldReceive('hasAttribute')
            ->with('action')
            ->andReturn(false)
            ->shouldReceive('setAttribute')
            ->with('action', 'URI/ ')
            ->getMock();

        $request = m::mock();

        $request->shouldReceive('getUri->getPath')
            ->andReturn('URI/');

        $request->shouldReceive('getUri->getQuery')
            ->andReturn('');

        $this->sut->setFormActionFromRequest($form, $request);
    }

    public function testRemoveOptionWithoutOption()
    {
        $index = 'blap';

        $options = [
            'foo' => 'bar',
            'bar' => 'baz'
        ];

        /** @var Select|\Mockery\MockInterface $element */
        $element = m::mock(Select::class);
        $element->shouldReceive('getValueOptions')
            ->andReturn($options);

        $this->sut->removeOption($element, $index);
    }

    public function testRemoveOptionWithOption()
    {
        $index = 'foo';

        $options = [
            'foo' => 'bar',
            'bar' => 'baz'
        ];

        /** @var Select|\Mockery\MockInterface $element */
        $element = m::mock(Select::class);
        $element->shouldReceive('getValueOptions')
            ->andReturn($options)
            ->shouldReceive('setValueOptions')
            ->with(['bar' => 'baz']);

        $this->sut->removeOption($element, $index);
    }

    public function testSetCurrentOptionWithoutCurrentOption()
    {
        $index = 'blap';

        $options = [
            'foo' => 'bar',
            'bar' => 'baz'
        ];

        /** @var Select|\Mockery\MockInterface $element */
        $element = m::mock(Select::class);
        $element->shouldReceive('getValueOptions')
            ->andReturn($options);

        $this->sut->setCurrentOption($element, $index);
    }

    public function testSetCurrentOptionWithCurrentOption()
    {
        $this->mockTransSrv
            ->shouldReceive('translate')->with('current.option.suffix')->andReturn('(current)')
            ->shouldReceive('translate')->with('baz')->andReturn('baz-translated');

        $index = 'bar';

        $options = [
            'foo' => 'bar',
            'bar' => 'baz'
        ];

        /** @var Select|\Mockery\MockInterface $element */
        $element = m::mock(Select::class);
        $element->shouldReceive('getValueOptions')
            ->andReturn($options)
            ->shouldReceive('setValueOptions')
            ->with(['foo' => 'bar', 'bar' => 'baz-translated (current)']);

        $this->sut->setCurrentOption($element, $index);
    }

    public function testCreateFormWithRequest()
    {
        $sut = m::mock(FormHelperService::class)->makePartial();

        $form = m::mock();

        $sut->shouldReceive('createForm')
            ->with('MyForm')
            ->andReturn($form)
            ->shouldReceive('setFormActionFromRequest')
            ->with($form, 'request');

        $this->assertEquals(
            $form,
            $sut->createFormWithRequest('MyForm', 'request')
        );
    }

    public function testGetValidator()
    {
        $validatorName = '\Zend\Validator\GreaterThan';

        $form      = m::mock('Zend\Form\Form');
        $validator = m::mock($validatorName);
        $element   = m::mock();
        $filter    = m::mock('\Zend\InputFilter\InputFilter');

        $form->shouldReceive('getInputFilter')->andReturn($filter);

        $filter->shouldReceive('get')->with('myelement')->andReturn($element);

        $element->shouldReceive('getValidatorChain')->andReturn(
            m::mock()
                ->shouldReceive('getValidators')
                ->andReturn(
                    [
                        ['instance' => $validator],
                        ['instance' => m::mock()],
                    ]
                )
                ->getMock()
        );

        $result = $this->sut->getValidator($form, 'myelement', $validatorName);

        $this->assertSame($validator, $result);
    }

    public function testGetValidatorNotFoundReturnsNull()
    {
        $form      = m::mock('Zend\Form\Form');
        $element   = m::mock();
        $filter    = m::mock('\Zend\InputFilter\InputFilter');

        $form->shouldReceive('getInputFilter')->andReturn($filter);

        $filter->shouldReceive('get')->with('myelement')->andReturn($element);

        $element->shouldReceive('getValidatorChain')->andReturn(
            m::mock()
                ->shouldReceive('getValidators')
                ->andReturn([])
                ->getMock()
        );

        $this->assertNull($this->sut->getValidator($form, 'myelement', 'MyValidator'));
    }

    public function testAttachValidator()
    {
        /** @var FormInterface|\Mockery\MockInterface $mockForm */
        $mockForm = m::mock(FormInterface::class);
        /** @var InputFilterInterface|\Mockery\MockInterface $mockForm */
        $mockInputFilter = m::mock(InputFilterInterface::class);
        $mockValidator = m::mock();
        $mockValidatorChain = m::mock();

        $mockForm->shouldReceive('getInputFilter')
            ->once()
            ->andReturn($mockInputFilter)
            ->shouldReceive('get')
            ->once()
            ->with('data')
            ->andReturnSelf();

        $mockInputFilter->shouldReceive('get')
            ->once()
            ->with('data')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->once()
            ->with('foo')
            ->andReturnSelf()
            ->shouldReceive('getValidatorChain')
            ->andReturn($mockValidatorChain);

        $mockValidatorChain->shouldReceive('attach')
            ->once()
            ->with($mockValidator);

        $this->sut->attachValidator($mockForm, 'data->foo', $mockValidator);
    }

    public function testSetDefaultDate()
    {
        // mocks
        $field      = m::mock();
        $dateHelper = m::mock();
        $today      = m::mock('\DateTime');

        // expectations
        $this->mockSm->shouldReceive('get')->with('Helper\Date')->andReturn($dateHelper);
        $field->shouldReceive('getValue')->andReturn('--');
        $dateHelper->shouldReceive('getDateObject')->andReturn($today);
        $field->shouldReceive('setValue')->with($today);

        $this->sut->setDefaultDate($field);
    }

    public function testSetDefaultDateFieldAlreadyHasValue()
    {
        // mocks
        $field      = m::mock();

        // expectations
        $this->mockSm->shouldReceive('get')->with('Helper\Date')->never();
        $field->shouldReceive('getValue')->andReturn('2015-04-09');
        $field->shouldReceive('setValue')->never();

        $this->sut->setDefaultDate($field);
    }

    public function testSaveFormState()
    {
        $mockForm = m::mock('Zend\Form\Form');
        $mockForm->shouldReceive('getName')->with()->once()->andReturn('FORM_NAME');

        $this->sut->saveFormState($mockForm, ['foo' => 'bar']);

        $sessionContainer = new \Zend\Session\Container('form_state');
        $this->assertEquals(['foo' => 'bar'], $sessionContainer->offsetGet('FORM_NAME'));
    }

    public function testRestoreFormState()
    {
        $mockForm = m::mock('Zend\Form\Form');
        $mockForm->shouldReceive('getName')->with()->twice()->andReturn('FORM_NAME');

        $sessionContainer = new \Zend\Session\Container('form_state');
        $sessionContainer->offsetSet('FORM_NAME', ['an' => 'array']);
        $mockForm->shouldReceive('setData')->with(['an' => 'array'])->once();

        $this->sut->restoreFormState($mockForm);
    }

    public function testRemoveValueOption()
    {
        $options = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C'
        ];

        /** @var Select $select */
        $select = m::mock(Select::class)->makePartial();
        $select->setValueOptions($options);

        $this->sut->removeValueOption($select, 'a');

        $this->assertEquals(['b' => 'B', 'c' => 'C'], $select->getValueOptions());
    }

    public function testSetFormValueOptionsFromList()
    {
        //Construct list in format expected from DB
        $list = array(
            'results' => array(
                '0' => array(
                    'id' => 'RB',
                    'description' => 'first result'
                    ),
                '1' => array(
                    'id' => 'EE',
                    'description' => 'second result'
                ),
                '2' =>array(
                    'id' => 'GG',
                    'description' => 'third result'
                ),
            )
        );

        $expected_value_options = array(
            'RB|first result' => 'first result',
            'EE|second result' => 'second result',
            'GG|third result' => 'third result',
        );

        //Construct form
        $element = new MultiCheckBox('optionList');

        $form = new Form('testForm');
        $form->add($element);

        $helperService = m::mock(\Common\Service\Helper\FormHelperService::class);

        $form = $helperService->shouldReceive('setFormValueOptionsFromList')
            ->with($form, 'optionList', $list, 'description');

        assert($form->get('optionList')->getOption('value_options') == $expected_value_options);
    }

    public function testTransformListIntoValueOptions()
    {
        //Construct list in format expected from DB
        $list = array(
            'results' => array(
                '0' => array(
                    'id' => 'RB',
                    'description' => 'first result'
                ),
                '1' => array(
                    'id' => 'EE',
                    'description' => 'second result'
                ),
                '2' =>array(
                    'id' => 'GG',
                    'description' => 'third result'
                ),
            )
        );

        $expected_value_options = array(
            'RB|first result' => 'first result',
            'EE|second result' => 'second result',
            'GG|third result' => 'third result',
        );

        $helperService = m::mock(\Common\Service\Helper\FormHelperService::class);

        $helperService->shouldReceive('transformListIntoValueOptions')
        ->with($list, 'description')
        ->andReturn($expected_value_options);
    }
}
