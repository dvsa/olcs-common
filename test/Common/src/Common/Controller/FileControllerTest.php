<?php

namespace CommonTest\Controller;

use Common\Controller\FileController;
use Common\Service\Cqrs\Response;
use Dvsa\Olcs\Transfer\Query as TransferQry;
use Mockery as m;
use Zend\Mvc\Controller\Plugin;

/**
 * @covers Common\Controller\FileController
 */
class FileControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var  m\MockInterface */
    private $mockParams;

    /** @var  m\MockInterface */
    private $sut;

    public function setUp()
    {
        $this->mockParams = m::mock(Plugin\Params::class . '[fromRoute, fromQuery]');

        $this->sut = m::mock(FileController::class . '[handleQuery, params, notFoundAction]');
        $this->sut->shouldReceive('params')->andReturn($this->mockParams);
    }

    public function testDownloadOk()
    {
        $id = '99999';

        $this->mockParams
            ->shouldReceive('fromRoute')->once()->with('identifier')->andReturn($id)
            ->shouldReceive('fromQuery')->once()->with('inline')->andReturn(1);

        $mockResp = m::mock(Response::class)
            ->shouldReceive('isNotFound')->once()->andReturn(false)
            ->shouldReceive('isOk')->once()->andReturn(true)
            ->shouldReceive('getHttpResponse')->once()->andReturn('EXPECTED')
            ->getMock();

        $this->sut
            ->shouldReceive('handleQuery')
            ->once()
            ->andReturnUsing(
                function ($arg) use ($id, $mockResp) {
                    static::assertInstanceOf(TransferQry\Document\Download ::class, $arg);

                    /** @var TransferQry\Document\Download $arg */
                    static::assertEquals($id, $arg->getIdentifier());
                    static::assertTrue($arg->isInline());

                    return $mockResp;
                }
            );

        static::assertEquals('EXPECTED', $this->sut->downloadAction());
    }

    public function testDownloadGuideOk()
    {
        $identifier = 'ABCDE12345';

        $this->mockParams
            ->shouldReceive('fromRoute')->once()->with('identifier')->andReturn(base64_encode($identifier))
            ->shouldReceive('fromQuery')->once()->with('inline')->andReturn(0);

        $mockResp = m::mock(Response::class)
            ->shouldReceive('isNotFound')->once()->andReturn(false)
            ->shouldReceive('isOk')->once()->andReturn(true)
            ->shouldReceive('getHttpResponse')->once()->andReturn('EXPECTED')
            ->getMock();

        $this->sut
            ->shouldReceive('handleQuery')
            ->once()
            ->andReturnUsing(
                function ($arg) use ($identifier, $mockResp) {
                    static::assertInstanceOf(TransferQry\Document\DownloadGuide::class, $arg);

                    /** @var TransferQry\Document\Download $arg */
                    static::assertEquals($identifier, $arg->getIdentifier());
                    static::assertFalse($arg->isInline());

                    return $mockResp;
                }
            );

        static::assertEquals('EXPECTED', $this->sut->downloadAction());
    }

    public function testFailNotFound()
    {
        $identifier = '8999';

        $this->mockParams
            ->shouldReceive('fromRoute')->once()->with('identifier')->andReturn($identifier)
            ->shouldReceive('fromQuery')->andReturn(null);

        $mockResp = m::mock(Response::class)
            ->shouldReceive('isNotFound')->once()->andReturn(true)
            ->getMock();

        $this->sut
            ->shouldReceive('handleQuery')->once()->andReturn($mockResp)
            ->shouldReceive('notFoundAction')->once()->andReturn('EXPECTED_ERR_NOT_FOUND');

        static::assertEquals('EXPECTED_ERR_NOT_FOUND', $this->sut->downloadAction());
    }

    public function testFailExceptionErrDownload()
    {
        $identifier = '8999';

        $this->mockParams
            ->shouldReceive('fromRoute')->once()->with('identifier')->andReturn($identifier)
            ->shouldReceive('fromQuery')->andReturn(null);

        $mockResp = m::mock(Response::class)
            ->shouldReceive('isNotFound')->once()->andReturn(false)
            ->shouldReceive('isOk')->once()->andReturn(false)
            ->getMock();

        $this->sut->shouldReceive('handleQuery')->once()->andReturn($mockResp);

        static::setExpectedException(\RuntimeException::class, 'Error downloading file');

        static::assertEquals('EXPECTED_ERR_NOT_FOUND', $this->sut->downloadAction());
    }
}
