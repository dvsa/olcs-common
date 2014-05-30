<?php

/**
 * Test FormActionController
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Controller;

/**
 * Test FormActionController
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class FormActionControllerTest extends \PHPUnit_Framework_TestCase
{


    public function testProcessAdd()
    {
        $data = ['foo' => 'bar', 'csrf' => 'ty', 'submit' => '123', 'fields' => '6767'];
        $data2 = ['foo' => 'bar', 'id' => '1234'];
        $entityName = 'Whatever';
        $result = ['id' => '1234'];

        $sut = $this->processAddEdit('POST', $data, $data2, $entityName, $result);
        $this->assertEquals($result, $sut->processAdd($data, $entityName));
    }

    public function testProcessEdit()
    {
        $data = ['foo' => 'bar', 'csrf' => 'ty', 'submit' => '123', 'fields' => '6767'];
        $data2 = ['foo' => 'bar'];
        $entityName = 'Whatever';
        $result = ['id' => '1234'];

        $sut = $this->processAddEdit('PUT', $data, $data2, $entityName, $result);
        $this->assertEquals($result, $sut->processEdit($data, $entityName));
    }

    public function processAddEdit($type, $data, $data2, $entityName, $result)
    {
        $sut = $this->getNewSut(['trimFormFields', 'makeRestCall', 'generateDocument']);
        $sut->expects($this->once())
            ->method('trimFormFields')
            ->with($this->equalTo($data))
            ->will($this->returnValue($data2));
        $sut->expects($this->once())
            ->method('makeRestCall')
            ->with($entityName, $type, $data2)
            ->will($this->returnValue($result));
        $sut->expects($this->once())
            ->method('generateDocument')
            ->with($data2)
            ->will($this->returnValue(null));

        return $sut;
    }

    /**
     * @dataProvider dataProviderIsButtonPressed
     */
    public function testIsButtonPressed($button, $pressed)
    {
        $data = [];
        $data['form-actions'][$button] = 'whatever';

        $request = $this->getMock('stdClass', ['isPost', 'getPost']);
        $request->expects($this->any())
                ->method('isPost')
                ->will($this->returnValue($pressed));
        $request->expects($this->any())
                ->method('getPost')
                ->will($this->returnValue($data));

        $sut = $this->getNewSut(['getRequest']);
        $sut->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $this->assertEquals($pressed, $sut->isButtonPressed($button));
    }

    public function dataProviderIsButtonPressed()
    {
        return [
            ['send', true],
            ['cancel', false]
        ];
    }

    public function getNewSut($methods = array())
    {
        $methods = array_merge($methods, ['log', 'addErrorMessage']);

        $mock = $this->getMock('\Common\Controller\FormActionController', $methods);

        return $mock;
    }
}
