<?php

namespace CommonTest\Controller\Traits;

use Common\Exception\File\InvalidMimeException;
use Common\Util\FileContent;
use CommonTest\Common\Controller\Traits\Stubs\GenericUploadStub;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Controller\Traits\GenericUpload
 */
class GenericUploadTest extends MockeryTestCase
{
    /** @var  GenericUploadStub */
    private $sut;
    /** @var  m\MockInterface */
    private $mockResp;

    public function setUp(): void
    {
        $this->mockResp = m::mock(\Laminas\Http\Response::class);

        $this->sut = new GenericUploadStub();
        $this->sut->stubResponse = $this->mockResp;
    }

    /**
     * @dataProvider dpTestUploadFile
     */
    public function testUploadFile($fileData, $data, $expect)
    {
        $this->mockResp
            ->shouldReceive('isOk')->andReturn(true)
            ->shouldReceive('isClientError')->andReturn(false);

        $this->assertTrue($this->sut->callUploadFile($fileData, $data));

        /** @var Upload $dto */
        $dto = $this->sut->stubResponse->dto;
        $this->assertInstanceOf(TransferCmd\Document\Upload::class, $dto);

        $this->assertEquals($expect['fileName'], $dto->getFilename());
        $this->assertInstanceOf(FileContent::class, $dto->getContent());
    }

    public function dpTestUploadFile()
    {
        return [
            [
                'fileData' => [
                    'tmp_name' => 'unit_FileName',
                    'type' => 'unit_Mime',
                ],
                'data' => [
                    'filename' => 'unit_FileName1',
                ],
                'expect' => [
                    'fileName' => 'unit_FileName1',
                ],
            ],
            [
                'fileData' => [
                    'name' => 'unit_Name2',
                    'tmp_name' => 'unit_FileName',
                    'type' => 'unit_Mime',
                ],
                'data' => [],
                'expect' => [
                    'fileName' => 'unit_Name2',
                ],
            ],
            [
                'fileData' => [
                    'filename' => 'unit_FileName3',
                    'tmp_name' => 'unit_FileName',
                    'type' => 'unit_Mime',
                ],
                'data' => [],
                'expect' => [
                    'fileName' => 'unit_FileName3',
                ],
            ],

        ];
    }

    public function testUploadFileServerFail()
    {
        $this->expectException(\Exception::class);

        $this->mockResp
            ->shouldReceive('isOk')->andReturn(false)
            ->shouldReceive('isClientError')->andReturn(false);

        $fileData = [
            'name' => 'unit_Name2',
            'tmp_name' => 'unit_FileName',
        ];
        $this->sut->callUploadFile($fileData, []);
    }

    public function testUploadFileInvalidMime()
    {
        $this->expectException(InvalidMimeException::class);

        $this->mockResp
            ->shouldReceive('isClientError')->andReturn(true)
            ->shouldReceive('getResult')->andReturn(
                [
                    'messages' => [
                        'ERR_MIME' => 'NOT_EXPECT_ERROR_MESSAGE',
                    ],
                ]
            );

        $fileData = [
            'name' => 'unit_Name2',
            'tmp_name' => 'unit_FileName',
        ];

        $this->sut->callUploadFile($fileData, []);
    }

    public function testUploadFileInvalidEbrsMime()
    {
        $this->expectException(InvalidMimeException::class);
        $this->expectExceptionMessage('EXPECT_ERROR_MESSAGE');

        $this->mockResp
            ->shouldReceive('isClientError')->andReturn(true)
            ->shouldReceive('getResult')->andReturn(
                [
                    'messages' => [
                        'ERR_EBSR_MIME' => 'EXPECT_ERROR_MESSAGE',
                    ],
                ]
            );

        $fileData = [
            'name' => 'unit_Name2',
            'tmp_name' => 'unit_FileName',
        ];

        $this->sut->callUploadFile($fileData, []);
    }

    public function testDeleteFile()
    {
        $this->sut->stubResponse = m::mock()->makePartial();
        $this->sut->stubResponse->shouldReceive('isOk')
            ->andReturn(true);

        $this->assertTrue($this->sut->callDeleteFile(123));

        $this->assertInstanceOf(TransferCmd\Document\DeleteDocument::class, $this->sut->stubResponse->dto);
        $this->assertEquals(123, $this->sut->stubResponse->dto->getId());
    }
}
