<?php

/**
 * Form Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\FormHelperService;
use Mockery as m;

/**
 * Form Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FormHelperServiceTest extends PHPUnit_Framework_TestCase
{
    public function testAlterElementLabelWithNoType()
    {
        $helper = new FormHelperService();

        $element = m::mock('\stdClass');
        $element->shouldReceive('getLabel')->andReturn('My label');
        $element->shouldReceive('setLabel')->with('Replaced label');

        $helper->alterElementLabel($element, 'Replaced label');
    }

    public function testAlterElementLabelWithAppend()
    {
        $helper = new FormHelperService();

        $element = m::mock('\stdClass');
        $element->shouldReceive('getLabel')->andReturn('My label');
        $element->shouldReceive('setLabel')->with('My labelAppended label');

        $helper->alterElementLabel($element, 'Appended label', 1);
    }

    public function testAlterElementLabelWithPrepend()
    {
        $helper = new FormHelperService();

        $element = m::mock('\stdClass');
        $element->shouldReceive('getLabel')->andReturn('My label');
        $element->shouldReceive('setLabel')->with('Prepended labelMy label');

        $helper->alterElementLabel($element, 'Prepended label', 2);
    }

    public function testCreateFormWithInvalidForm()
    {
        $helper = new FormHelperService();

        try {
            $helper->createForm('NotFound');
        } catch (\RuntimeException $ex) {
            $this->assertEquals('Form does not exist: NotFound', $ex->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }

    public function testCreateFormWithValidForm()
    {
        $helper = new FormHelperService();

        $form = m::mock('Common\Form\Model\Form\MyFakeFormTest');

        $form->shouldReceive('add')
            ->with(
                array(
                    'type' => 'Zend\Form\Element\Csrf',
                    'name' => 'security',
                    'options' => array(
                        'csrf_options' => array(
                            'messageTemplates' => array(
                                'notSame' => 'csrf-message'
                            ),
                            'timeout' => 600
                        )
                    )
                )
            )
            ->shouldReceive('add')
            ->with(
                array(
                    'type' => '\Zend\Form\Element\Button',
                    'name' => 'form-actions[submit]',
                    'options' => array(
                        'label' => 'Continue'
                    ),
                    'attributes' => array(
                        'type' => 'submit',
                        'class' => 'visually-hidden'
                    )
                )
            );

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $builder = m::mock('\stdClass');

        $sm->shouldReceive('get')
            ->once()
            ->with('FormAnnotationBuilder')
            ->andReturn($builder);

        $builder->shouldReceive('createForm')
            ->once()
            ->with('Common\Form\Model\Form\MyFakeFormTest')
            ->andReturn($form);

        $helper->setServiceLocator($sm);

        $result = $helper->createForm('MyFakeFormTest');

        $this->assertEquals($form, $result);
    }

    public function testCreateFormWithValidFormAndNoCsrfOrContinue()
    {
        $helper = new FormHelperService();

        $form = m::mock('Common\Form\Model\Form\MyFakeFormTest');

        $form->shouldReceive('add')->never();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $builder = m::mock('\stdClass');

        $sm->shouldReceive('get')
            ->once()
            ->with('FormAnnotationBuilder')
            ->andReturn($builder);

        $builder->shouldReceive('createForm')
            ->once()
            ->with('Common\Form\Model\Form\MyFakeFormTest')
            ->andReturn($form);

        $helper->setServiceLocator($sm);

        $result = $helper->createForm('MyFakeFormTest', false, false);

        $this->assertEquals($form, $result);
    }

    public function testProcessAddressLookupWithNoPostcodeOrAddressSelected()
    {
        $helper = new FormHelperService();

        $form = m::mock('Zend\Form\Form');

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('getPost')
            ->andReturn([]);

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
            $helper->processAddressLookupForm($form, $request)
        );
    }

    public function testProcessAddressLookupWithAddressSelected()
    {
        $helper = new FormHelperService();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $addressData = m::mock('\stdClass');
        $addressData->shouldReceive('getAddressForUprn')
            ->with(['address1'])
            ->andReturn('address_1234');

        $addressHelper = m::mock('\stdClass');
        $addressHelper->shouldReceive('formatPostalAddressFromBs7666')
            ->with('address_1234')
            ->andReturn('formatted1');

        $sm->shouldReceive('get')
            ->with('Data\Address')
            ->andReturn($addressData)
            ->getMock()
            ->shouldReceive('get')
            ->with('Helper\Address')
            ->andReturn($addressHelper);

        $helper->setServiceLocator($sm);

        $form = m::mock('Zend\Form\Form');

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
            );

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
            ->andReturn([$fieldset])
            ->shouldReceive('setData')
            ->with(
                ['address' => 'formatted1']
            );

        $this->assertTrue(
            $helper->processAddressLookupForm($form, $request)
        );
    }

    public function testProcessAddressLookupWithPostcodeSearch()
    {
        $helper = new FormHelperService();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $address = m::mock('\stdClass');
        $address->shouldReceive('getAddressesForPostcode')
            ->andReturn(['address1', 'address2']);

        $addressHelper = m::mock('\stdClass');
        $addressHelper->shouldReceive('formatAddressesForSelect')
            ->with(['address1', 'address2'])
            ->andReturn(['formatted1', 'formatted2']);

        $sm->shouldReceive('get')
            ->with('Data\Address')
            ->andReturn($address)
            ->getMock()
            ->shouldReceive('get')
            ->with('Helper\Address')
            ->andReturn($addressHelper);

        $helper->setServiceLocator($sm);

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
            );

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
            $helper->processAddressLookupForm($form, $request)
        );
    }

    public function testProcessAddressLookupWithEmptyAddresses()
    {
        $helper = new FormHelperService();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $address = m::mock('\stdClass');
        $address->shouldReceive('getAddressesForPostcode')
            ->andReturn([]);

        $sm->shouldReceive('get')
            ->with('Data\Address')
            ->andReturn($address);

        $helper->setServiceLocator($sm);

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
            );

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
            ->with(array('No addresses found for postcode'));

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
            $helper->processAddressLookupForm($form, $request)
        );
    }

    public function testProcessAddressLookupWithEmptyPostcodeSearch()
    {
        $helper = new FormHelperService();

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
            );

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
            $helper->processAddressLookupForm($form, $request)
        );
    }

    public function testDisableElementWithNestedSelector()
    {
        $helper = new FormHelperService();

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

        $helper->disableElement($form, 'foo->bar');
    }

    public function testDisableElementWithDateInput()
    {
        $helper = new FormHelperService();

        $form = m::mock('Zend\Form\Form');

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

        $form->shouldReceive('getInputFilter')
            ->andReturn($filter)
            ->getMock()
            ->shouldReceive('get')
            ->with('bar')
            ->andReturn($element);

        $helper->disableElement($form, 'bar');
    }

    public function testDisableDateElement()
    {
        $helper = new FormHelperService();

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

        $helper->disableDateElement($element);
    }

    public function testRemove()
    {
        $helper = new FormHelperService();

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

        $helper->remove($form, 'foo->bar');
    }

    public function testDisableElements()
    {
        $helper = new FormHelperService();

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

        $helper->disableElements($form);
    }

    public function testDisableValidation()
    {
        $helper = new FormHelperService();

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

        $helper->disableValidation($filter);
    }

    public function testDisableEmptyValidation()
    {
        $helper = new FormHelperService();

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

        $helper->disableEmptyValidation($form);
    }

    public function testPopulateFormTable()
    {
        $helper = new FormHelperService();

        $table = m::mock('Common\Service\Table\TableBuilder');
        $table->shouldReceive('getRows')
            ->andReturn([1,2,3,4]);

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

        $helper->populateFormTable($fieldset, $table, 'fieldset');
    }

    public function testLockElement()
    {
        $helper = new FormHelperService();

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $translator = m::mock('\stdClass');
        $translator->shouldReceive('translate')
            ->with('message')
            ->andReturn('translated');

        $renderer = m::mock('\stdClass');
        $renderer->shouldReceive('render')
            ->andReturn('template');

        $sm->shouldReceive('get')
            ->once()
            ->with('ViewRenderer')
            ->andReturn($renderer)
            ->getMock()
            ->shouldReceive('get')
            ->with('Helper\Translation')
            ->andReturn($translator);

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

        $helper->setServiceLocator($sm);

        $helper->lockElement($element, 'message');
    }

    public function testRemoveFieldLiset()
    {
        $helper = new FormHelperService();

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

        $helper->removeFieldList($form, 'foo', ['bar']);
    }
}
