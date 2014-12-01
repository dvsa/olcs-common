<?php

/**
 * File Upload Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\FileUploadHelperService;
use Mockery as m;

/**
 * File Upload Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FileUploadHelperServiceTest extends MockeryTestCase
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

    public function testGetElementFromFormAndSelector()
    {
        $form = m::mock('Zend\Form\Form');
        $fieldset = m::mock('Zend\Form\Fieldset');

        $form->shouldReceive('get')
            ->with('foo')
            ->andReturn($fieldset);

        $fieldset->shouldReceive('get')
            ->with('bar')
            ->andReturn('fakeElement');

        $helper = new FileUploadHelperService();

        $helper->setForm($form);
        $helper->setSelector('foo->bar');

        $this->assertEquals('fakeElement', $helper->getElement());
    }

    public function testProcessWithGetRequestAndNoLoadCallback()
    {
        $helper = new FileUploadHelperService();

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('isPost')->andReturn(false);

        $helper->setRequest($request);

        $this->assertFalse($helper->process());
    }

    public function testProcessWithGetRequestAndNotCallableLoadCallback()
    {
        $helper = new FileUploadHelperService();

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('isPost')->andReturn(false);

        $helper->setRequest($request);
        $helper->setLoadCallback(true); // not callable... obviously

        try {
            $helper->process();
        } catch (\Common\Exception\ConfigurationException $ex) {
            $this->assertEquals('Load data callback is not callable', $ex->getMessage());
            return;
        }
        $this->fail('Expected exception not raised');
    }

    public function testProcessWithPostAndValidFileUpload()
    {
        $helper = new FileUploadHelperService();

        $file = tempnam("/tmp", "fuhs");
        touch($file);

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('isPost')->andReturn(true);

        $postData = [
            'my-file' => [
                'file-controls' => [
                    'upload' => true
                ]
            ]
        ];

        $fileData = [
            'my-file' => [
                'file-controls' => [
                    'file' => [
                        'error' => 0,
                        'tmp_name' => $file
                    ]
                ]
            ]
        ];
        $request->shouldReceive('getPost')
            ->andReturn($postData);

        $request->shouldReceive('getFiles')
            ->andReturn($fileData);

        $helper->setRequest($request);
        $helper->setSelector('my-file');
        $helper->setUploadCallback(
            function ($data) use ($file) {
                $expected = [
                    'error' => 0,
                    'tmp_name' => $file
                ];
                $this->assertEquals($expected, $data);
            }
        );

        $this->assertEquals(true, $helper->process());

        unlink($file);
    }

    /**
     * @dataProvider fileUploadProvider
     */
    public function testProcessWithPostAndInvalidFileUpload($error, $message)
    {
        $helper = new FileUploadHelperService();

        $file = tempnam("/tmp", "fuhs");
        touch($file);

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('isPost')->andReturn(true);

        $form = m::mock('Zend\Form\Form');
        $form->shouldReceive('setMessages')
            ->once()
            ->with(
                [
                    'my-file' => [
                        '__messages__' => [$message]
                    ]

                ]
            );

        $postData = [
            'my-file' => [
                'file-controls' => [
                    'upload' => true
                ]
            ]
        ];

        $fileData = [
            'my-file' => [
                'file-controls' => [
                    'file' => [
                        'error' => $error,
                        'tmp_name' => $file
                    ]
                ]
            ]
        ];
        $request->shouldReceive('getPost')
            ->andReturn($postData);

        $request->shouldReceive('getFiles')
            ->andReturn($fileData);

        $helper->setRequest($request);
        $helper->setSelector('my-file');
        $helper->setForm($form);
        $helper->setUploadCallback(
            function ($data) use ($file) {
                $expected = [
                    'error' => 0,
                    'tmp_name' => $file
                ];
                $this->assertEquals($expected, $data);
            }
        );

        $this->assertEquals(true, $helper->process());

        unlink($file);
    }

    public function fileUploadProvider()
    {
        return [
            [UPLOAD_ERR_PARTIAL, 'File was only partially uploaded'],
            [UPLOAD_ERR_NO_FILE, 'Please select a file to upload'],
            [UPLOAD_ERR_INI_SIZE, 'The file was too large to upload'],
            [UPLOAD_ERR_NO_TMP_DIR, 'An unexpected error occurred while uploading the file']
        ];
    }

    public function testProcessWithPostAndFileDeletions()
    {
        $helper = new FileUploadHelperService();

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('isPost')->andReturn(true);

        $postData = [
            'my-file' => [
                'list' => [
                    'file1' => [
                        'remove' => true,
                        'id' => 123
                    ]
                ]
            ]
        ];

        $request->shouldReceive('getPost')
            ->andReturn($postData);

        $fieldset = m::mock('Zend\Form\Fieldset');
        $fieldset->shouldReceive('getName')
            ->andReturn('file1');

        $listElement = m::mock('\stdClass');
        $listElement->shouldReceive('getFieldsets')
            ->andReturn([$fieldset])
            ->getMock()
            ->shouldReceive('remove')
            ->with('file1');

        $element = m::mock('\stdClass');
        $element->shouldReceive('get')
            ->with('list')
            ->andReturn($listElement);

        $helper->setElement($element);
        $helper->setRequest($request);
        $helper->setSelector('my-file');
        $helper->setDeleteCallback(
            function ($id) {
                $this->assertEquals(123, $id);
                return true;
            }
        );

        $this->assertEquals(true, $helper->process());
    }

    public function testProcessWithPostAndFileDeletionsWithNoDeletionsToDelete()
    {
        $helper = new FileUploadHelperService();

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('isPost')->andReturn(true);

        $postData = [
            'my-file' => [
                'list' => [
                    'file1' => [
                        'remove' => true,
                        'id' => 123
                    ]
                ]
            ]
        ];

        $request->shouldReceive('getPost')
            ->andReturn($postData);

        $listElement = m::mock('\stdClass');
        $listElement->shouldReceive('getFieldsets')
            ->andReturn([])
            ->getMock()
            ->shouldReceive('remove')
            ->with('file1');

        $element = m::mock('\stdClass');
        $element->shouldReceive('get')
            ->with('list')
            ->andReturn($listElement);

        $helper->setElement($element);
        $helper->setRequest($request);
        $helper->setSelector('my-file');
        $helper->setDeleteCallback(
            function () {
            }
        );

        $this->assertEquals(false, $helper->process());
    }

    public function testProcessWithPostAndFileDeletionsWithNoList()
    {
        $helper = new FileUploadHelperService();

        $request = m::mock('Zend\Http\Request');
        $request->shouldReceive('isPost')->andReturn(true);

        $postData = [
            'my-file' => []
        ];

        $request->shouldReceive('getPost')
            ->andReturn($postData);

        $helper->setRequest($request);
        $helper->setSelector('my-file');
        $helper->setDeleteCallback(
            function () {
            }
        );

        $this->assertEquals(false, $helper->process());
    }
}
