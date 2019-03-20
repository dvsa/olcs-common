<?php

namespace CommonTest\Service\Cqrs\Adapter;

use Common\Service\Cqrs\Adapter\Curl;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class CurlTest extends MockeryTestCase
{
    public function testWrite()
    {
        /** @var Curl | m\MockInterface $sut */
        $sut = m::mock(Curl::class)->makePartial();

        $host = 'unitHost';

        $mockUri = m::mock(\Zend\Uri\Uri::class)->makePartial()
            ->shouldReceive('getHost')->andReturn($host)
            ->shouldReceive('getPort')->andReturn(80)
            ->shouldReceive('__toString')->andReturn('file://'.__FILE__)
            ->getMock();

        $sut->connect($host);
        $sut->write('GET', $mockUri);

        //  second request as stream

        //  it should throw exception, response after second request must be empty.
        //  curl returns headers and call CURLOPT_HEADERFUNCTION function only for http|ftp protocols,
        //  so need need make request to http host, and that is incorrect in unit tests.
        //  in real life it will not empty, because curl will put headers in response.
        /*$this->expectException(
            \Zend\Http\Client\Adapter\Exception\RuntimeException::class,
            'Error in cURL request: '
        );*/

        $vfs = vfsStream::setup('temp');
        $tmpFileTrg = vfsStream::newFile('stream')->withContent('unit_test_content_2')->at($vfs)->url();

        $sut->setOutputStream(fopen($tmpFileTrg, 'wb'));
        $sut->write('GET', $mockUri);
    }
}
