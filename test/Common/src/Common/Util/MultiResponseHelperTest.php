<?php


namespace CommonTest\Controller\Util;

use PHPUnit_Framework_TestCase as TestCase;
use Common\Util\MultiResponseHelper;
use Zend\Http\Response;

/**
 * Class MultiResponseHelperTest
 * @package CommonTest\Controller\Util
 */
class MultiResponseHelperTest extends TestCase
{
    public function testHandleResponse()
    {
        $responseData = [
            'Data' => [
                'resp1' => [
                    'Data' => 'some data'
                ],
                'resp2' => [
                    'Data' => 'some more data'
                ]
            ]
        ];

        $response = new Response();
        $response->setContent(json_encode($responseData));
        $response->setStatusCode(Response::STATUS_CODE_207);

        $sut = new MultiResponseHelper();
        $sut->setMethod('POST');
        $sut->setResponse($response);
        $data = $sut->handleResponse();

        $this->assertEquals(['resp1' => 'some data', 'resp2' => 'some more data'], $data);
    }

    public function testHandleNone207Response()
    {
        $responseData = [
            'Data' => [
                'resp1' => [
                    'Data' => 'some data'
                ],
                'resp2' => [
                    'Data' => 'some more data'
                ]
            ]
        ];

        $response = new Response();
        $response->setContent(json_encode($responseData));
        $response->setStatusCode(Response::STATUS_CODE_201);

        $sut = new MultiResponseHelper();
        $sut->setMethod('POST');
        $sut->setResponse($response);
        $data = $sut->handleResponse();

        $this->assertInternalType('array', $data);
    }

    public function testHandleResponseNoneValid2()
    {
        $responseData = [
            'Data' => [
            ]
        ];

        $response = new Response();
        $response->setContent(json_encode($responseData));
        $response->setStatusCode(Response::STATUS_CODE_207);

        $sut = new MultiResponseHelper();
        $sut->setMethod('POST');
        $sut->setResponse($response);
        $data = $sut->handleResponse();

        $this->assertFalse($data);
    }
}
