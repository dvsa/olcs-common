<?php

namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\FileUploadHelperService;
use Mockery as m;

/**
 * @covers \Common\Service\Helper\FileUploadHelperService
 */
class FileUploadHelperServiceTest extends MockeryTestCase
{
    /** @var  FileUploadHelperService */
    private $sut;
    /** @var  m\MockInterface */
    private $mockRequest;
    /** @var  m\MockInterface */
    private $mockForm;
    /** @var  m\MockInterface | \Zend\ServiceManager\ServiceLocatorInterface */
    private $mockSm;

    public function setUp()
    {
        $this->mockRequest = m::mock(\Zend\Http\Request::class);
        $this->mockForm = m::mock(\Zend\Form\Form::class);

        $this->mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);

        $this->sut = new FileUploadHelperService();
        $this->sut->setRequest($this->mockRequest);
        $this->sut->setForm($this->mockForm);
        $this->sut->setServiceLocator($this->mockSm);
    }

    public function testSetGetForm()
    {
        $this->assertEquals(
            'fakeForm',
            $this->sut->setForm('fakeForm')->getForm()
        );
    }

    public function testSetGetSelector()
    {
        $this->assertEquals(
            'fakeSelector',
            $this->sut->setSelector('fakeSelector')->getSelector()
        );
    }

    public function testSetGetCountSelector()
    {
        $this->assertEquals(
            'fakeCountSelector',
            $this->sut->setCountSelector('fakeCountSelector')->getCountSelector()
        );
    }

    public function testSetGetUploadCallback()
    {
        $this->assertEquals(
            'fakeUploadCallback',
            $this->sut->setUploadCallback('fakeUploadCallback')->getUploadCallback()
        );
    }

    public function testSetGetDeleteCallback()
    {
        $this->assertEquals(
            'fakeDeleteCallback',
            $this->sut->setDeleteCallback('fakeDeleteCallback')->getDeleteCallback()
        );
    }

    public function testSetGetLoadCallback()
    {
        $this->assertEquals(
            'fakeLoadCallback',
            $this->sut->setLoadCallback('fakeLoadCallback')->getLoadCallback()
        );
    }

    public function testSetGetRequest()
    {
        $this->assertEquals(
            'fakeRequest',
            $this->sut->setRequest('fakeRequest')->getRequest()
        );
    }

    public function testSetGetElement()
    {
        $this->assertEquals(
            'fakeElement',
            $this->sut->setElement('fakeElement')->getElement()
        );
    }

    public function testGetElementFromFormAndSelector()
    {
        $fieldset = m::mock('Zend\Form\Fieldset');

        $this->mockForm->shouldReceive('get')
            ->with('foo')
            ->andReturn($fieldset);

        $fieldset->shouldReceive('get')
            ->with('bar')
            ->andReturn('fakeElement');

        $this->sut->setSelector('foo->bar');

        $this->assertEquals('fakeElement', $this->sut->getElement());
    }

    public function testProcessWithGetRequestAndNoLoadCallback()
    {
        $this->mockRequest->shouldReceive('isPost')->andReturn(false);

        $this->assertFalse($this->sut->process());
    }

    public function testProcessWithGetRequestPopulatesFileCount()
    {
        $this->mockRequest->shouldReceive('isPost')->andReturn(false);

        $this->sut->setCountSelector('my-hidden-field');
        $this->sut->setSelector('my-files');

        $this->sut->setLoadCallback(
            function () {
                return ['array-of-files'];
            }
        );

        $mockUrlHelper = m::mock();
        $this->sut->setServiceLocator(
            m::mock('Zend\ServiceManager\ServiceLocatorInterface')
                ->shouldReceive('get')
                    ->with('Helper\Url')
                    ->andReturn($mockUrlHelper)
                ->getMock()
        );

        $fieldset = m::mock() // multiple file upload fieldset
            ->shouldReceive('get')
                ->with('list')
                ->andReturn(
                    m::mock()
                        ->shouldReceive('setFiles')
                            ->with(['array-of-files'], $mockUrlHelper)
                        ->getMock()
                )
            ->getMock();

        $fileCountfield = m::mock()->shouldReceive('setValue')->with(1)->getMock();

        $this->mockForm
            ->shouldReceive('get')
                ->with('my-files')
                ->andReturn($fieldset)
            ->shouldReceive('get')
                ->with('my-hidden-field')
                ->andReturn($fileCountfield)
            ->getMock();

        $this->assertFalse($this->sut->process());
    }

    public function testProcessWithGetRequestAndNotCallableLoadCallback()
    {
        $this->mockRequest->shouldReceive('isPost')->andReturn(false);

        $this->sut->setLoadCallback(true); // not callable... obviously

        try {
            $this->sut->process();
        } catch (\Common\Exception\ConfigurationException $ex) {
            $this->assertEquals('Load data callback is not callable', $ex->getMessage());
            return;
        }
        $this->fail('Expected exception not raised');
    }

    public function testProcessWithPostAndValidFileUpload()
    {
        $file = tempnam(sys_get_temp_dir(), "fuhs");
        touch($file);

        $this->mockVirusScan($file, true);
        $this->mockCfs([]);

        //  mock request
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

        $this->mockRequest
            ->shouldReceive('isPost')->andReturn(true)
            ->shouldReceive('getPost')->andReturn($postData)
            ->shouldReceive('getFiles')->andReturn($fileData);

        $this->sut->setSelector('my-file');
        $this->sut->setUploadCallback(
            function ($data) use ($file) {
                $expected = [
                    'error' => 0,
                    'tmp_name' => $file
                ];
                $this->assertEquals($expected, $data);
            }
        );

        $this->assertEquals(true, $this->sut->process());

        unlink($file);
    }

    private function mockVirusScan($file, $isClean)
    {
        $mockScan = m::mock(\Common\Service\AntiVirus\Scan::class)
            ->shouldReceive('isEnabled')->with()->once()->andReturn(true)
            ->shouldReceive('isClean')->with($file)->once()->andReturn($isClean)
            ->getMock();

        $this->mockSm
            ->shouldReceive('get')->with(\Common\Service\AntiVirus\Scan::class)->once()->andReturn($mockScan);
    }

    private function mockCfs($cfg)
    {
        $cfg = $cfg +
            [
                'document_share' => [
                    'max_upload_size' => '10MB',
                ],
            ];

        $this->mockSm
            ->shouldReceive('get')->with('Config')->once()->andReturn($cfg);
    }

    /**
     * @dataProvider fileUploadProvider
     */
    public function testProcessWithPostAndInvalidFileUpload($error, $message)
    {
        $file = tempnam("/tmp", "fuhs");
        touch($file);

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

        $this->mockRequest
            ->shouldReceive('isPost')->andReturn(true)
            ->shouldReceive('getPost')->andReturn($postData)
            ->shouldReceive('getFiles')->andReturn($fileData);

        $this->mockForm
            ->shouldReceive('setMessages')
            ->once()
            ->with(
                [
                    'my-file' => [
                        '__messages__' => [$message]
                    ]

                ]
            );

        $this->mockCfs([]);

        $this->sut->setSelector('my-file');
        $this->sut->setServiceLocator($this->mockSm);
        $this->sut->setUploadCallback(
            function ($data) use ($file) {
                $expected = [
                    'error' => 0,
                    'tmp_name' => $file
                ];
                $this->assertEquals($expected, $data);
            }
        );

        $this->assertEquals(true, $this->sut->process());

        unlink($file);
    }

    public function fileUploadProvider()
    {
        return [
            [UPLOAD_ERR_PARTIAL, 'message.file-upload-error.'. UPLOAD_ERR_PARTIAL],
            [UPLOAD_ERR_NO_FILE, 'message.file-upload-error.'. UPLOAD_ERR_NO_FILE],
            [UPLOAD_ERR_INI_SIZE, 'message.file-upload-error.'. UPLOAD_ERR_INI_SIZE],
            [UPLOAD_ERR_NO_TMP_DIR, 'message.file-upload-error.'. UPLOAD_ERR_NO_TMP_DIR],
        ];
    }

    public function testProcessWithPostFileMissing()
    {
        $file = 'foo';

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
                        'error' => UPLOAD_ERR_OK,
                        'tmp_name' => $file
                    ]
                ]
            ]
        ];

        $this->mockRequest
            ->shouldReceive('isPost')->andReturn(true)
            ->shouldReceive('getPost')->andReturn($postData)
            ->shouldReceive('getFiles')->andReturn($fileData);

        $this->mockForm->shouldReceive('setMessages')
            ->once()
            ->with(
                [
                    'my-file' => [
                        '__messages__' => ['message.file-upload-error.missing']
                    ]

                ]
            );

        $this->sut->setSelector('my-file');
        $this->sut->setUploadCallback(
            function ($data) use ($file) {
                $expected = [
                    'error' => 0,
                    'tmp_name' => $file
                ];
                $this->assertEquals($expected, $data);
            }
        );

        $this->assertEquals(false, $this->sut->process());
    }

    public function testProcessWithPostFileWithVirus()
    {
        $file = __FILE__;

        $this->mockVirusScan($file, false);
        $this->mockCfs([]);

        $this->mockForm
            ->shouldReceive('setMessages')
            ->once()
            ->with(
                [
                    'my-file' => [
                        '__messages__' => ['message.file-upload-error.virus']
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
                        'error' => UPLOAD_ERR_OK,
                        'tmp_name' => $file
                    ]
                ]
            ]
        ];
        $this->mockRequest
            ->shouldReceive('isPost')->andReturn(true)
            ->shouldReceive('getPost')->andReturn($postData)
            ->shouldReceive('getFiles')->andReturn($fileData);

        $this->sut->setSelector('my-file');
        $this->sut->setUploadCallback(
            function ($data) use ($file) {
                $expected = [
                    'error' => 0,
                    'tmp_name' => $file
                ];
                $this->assertEquals($expected, $data);
            }
        );

        $this->assertEquals(false, $this->sut->process());
    }

    public function testProcessWithPostFileUploadAnyExpection()
    {
        $file = __FILE__;

        $postData = [
            'my-file' => [
                'file-controls' => [
                    'upload' => true,
                ],
            ],
        ];
        $fileData = [
            'my-file' => [
                'file-controls' => [
                    'file' => [
                        'error' => UPLOAD_ERR_OK,
                        'tmp_name' => $file,
                    ],
                ],
            ]
        ];

        $this->mockRequest
            ->shouldReceive('isPost')->andReturn(true)
            ->shouldReceive('getPost')->andReturn($postData)
            ->shouldReceive('getFiles')->andReturn($fileData);

        $this->mockForm
            ->shouldReceive('setMessages')
            ->once()
            ->with(
                [
                    'my-file' => [
                        '__messages__' => ['message.file-upload-error.any'],
                    ],
                ]
            );

        $this->mockVirusScan($file, true);
        $this->mockCfs([]);

        $this->sut
            ->setSelector('my-file')
            ->setUploadCallback(
                function () {
                    throw new \Exception('any error');
                }
            );

        static::assertEquals(false, $this->sut->process());
    }

    public function testProcessWithPostAndFileDeletions()
    {
        $this->mockRequest->shouldReceive('isPost')->andReturn(true);

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

        $this->mockRequest->shouldReceive('getPost')
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

        $this->sut->setElement($element);
        $this->sut->setSelector('my-file');
        $this->sut->setDeleteCallback(
            function ($id) {
                $this->assertEquals(123, $id);
                return true;
            }
        );

        $this->sut->setCountSelector('my-hidden-field');

        $fileCountfield = m::mock()
            ->shouldReceive('getValue')->andReturn('3')
            ->shouldReceive('setValue')->with(2)
            ->getMock();

        $this->mockForm
            ->shouldReceive('get')
                ->with('my-hidden-field')
                ->andReturn($fileCountfield)
            ->getMock();

        $this->assertEquals(true, $this->sut->process());
    }

    public function testProcessWithPostAndFileDeletionsWithNoDeletionsToDelete()
    {
        $this->mockRequest->shouldReceive('isPost')->andReturn(true);

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

        $this->mockRequest->shouldReceive('getPost')
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

        $this->sut->setElement($element);
        $this->sut->setSelector('my-file');
        $this->sut->setDeleteCallback(
            function () {
            }
        );

        $this->assertEquals(false, $this->sut->process());
    }

    public function testProcessWithPostAndFileDeletionsWithNoList()
    {
        $this->mockRequest->shouldReceive('isPost')->andReturn(true);

        $postData = [
            'my-file' => []
        ];

        $this->mockRequest->shouldReceive('getPost')
            ->andReturn($postData);

        $this->sut->setSelector('my-file');
        $this->sut->setDeleteCallback(
            function () {
            }
        );

        $this->assertEquals(false, $this->sut->process());
    }
}
