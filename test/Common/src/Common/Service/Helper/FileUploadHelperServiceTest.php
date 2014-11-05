<?php

/**
 * File Upload Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\FileUploadHelperService;
use Mockery as m;

/**
 * File Upload Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FileUploadHelperServiceTest extends PHPUnit_Framework_TestCase
{
    public function testSetGetForm()
    {
        $helper = new FileUploadHelperService();

        $this->assertEquals(
            'fakeForm',
            $helper->setForm('fakeForm')->getForm()
        );
    }

    public function testSetGetSelector()
    {
        $helper = new FileUploadHelperService();

        $this->assertEquals(
            'fakeSelector',
            $helper->setSelector('fakeSelector')->getSelector()
        );
    }

    public function testSetGetUploadCallback()
    {
        $helper = new FileUploadHelperService();

        $this->assertEquals(
            'fakeUploadCallback',
            $helper->setUploadCallback('fakeUploadCallback')->getUploadCallback()
        );
    }

    public function testSetGetDeleteCallback()
    {
        $helper = new FileUploadHelperService();

        $this->assertEquals(
            'fakeDeleteCallback',
            $helper->setDeleteCallback('fakeDeleteCallback')->getDeleteCallback()
        );
    }

    public function testSetGetLoadCallback()
    {
        $helper = new FileUploadHelperService();

        $this->assertEquals(
            'fakeLoadCallback',
            $helper->setLoadCallback('fakeLoadCallback')->getLoadCallback()
        );
    }

    public function testSetGetRequest()
    {
        $helper = new FileUploadHelperService();

        $this->assertEquals(
            'fakeRequest',
            $helper->setRequest('fakeRequest')->getRequest()
        );
    }

    public function testSetGetElement()
    {
        $helper = new FileUploadHelperService();

        $this->assertEquals(
            'fakeElement',
            $helper->setElement('fakeElement')->getElement()
        );
    }

    public function testProcessWithGetRequestAndNoLoadCallback()
    {
        $helper = new FileUploadHelperService();

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('isPost')->andReturn(false);

        $helper->setRequest($request);

        $this->assertFalse($helper->process());
    }
}
