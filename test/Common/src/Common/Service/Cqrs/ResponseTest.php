<?php

namespace CommonTest\Service\Cqrs;

use Common\Service\Cqrs\Response;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\Http\Response as HttpResponse;

/**
 * @covers Common\Service\Cqrs\Response
 */
class ResponseTest extends MockeryTestCase
{
    /** @var  Response */
    private $sut;

    /** @var  m\MockInterface|HttpResponse */
    private $mockHttpResp;

    public function setUp()
    {
        $this->mockHttpResp = m::mock(HttpResponse::class);

        $this->sut = new Response($this->mockHttpResp);
    }

    public function testGetSet()
    {
        $expectResult = ['foo' => 'unit_Val'];
        $expectBody = json_encode($expectResult);

        $this->mockHttpResp
            ->shouldReceive('isClientError')->once()->andReturn('unit_IsCliErr')
            ->shouldReceive('isServerError')->once()->andReturn('unit_isSrvError')
            ->shouldReceive('isNotFound')->once()->andReturn('unit_IsNotFnd')
            ->shouldReceive('isSuccess')->once()->andReturn('unit_isOk')
            ->shouldReceive('getStatusCode')->twice()->andReturn('unit_Code')
            ->shouldReceive('getBody')->twice()->andReturn($expectBody)
            ->shouldReceive('getReasonPhrase')->once()->andReturn('unit_Phrase');

        static::assertEquals('unit_IsCliErr', $this->sut->isClientError());
        static::assertEquals('unit_isSrvError', $this->sut->isServerError());
        static::assertEquals('unit_IsNotFnd', $this->sut->isNotFound());
        static::assertEquals('unit_isOk', $this->sut->isOk());
        static::assertEquals('unit_Code', $this->sut->getStatusCode());
        static::assertEquals($expectBody, $this->sut->getBody());
        static::assertEquals($expectResult, $this->sut->getResult());

        static::assertSame($this->mockHttpResp, $this->sut->getHttpResponse());

        //  test direct set of result
        $this->sut->setResult('unit_Result');
        static::assertEquals('unit_Result', $this->sut->getResult());

        //  test to string
        static::assertEquals(
            "Status = unit_Code unit_Phrase\n" .
            "Response = unit_Result",
            (string) $this->sut
        );
    }
}
