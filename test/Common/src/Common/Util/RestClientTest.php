<?php

/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */
namespace CommonTest\Util;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Header\Accept;
use Laminas\Http\Header\AcceptLanguage;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Stdlib\ParametersInterface;
use Laminas\Uri\Http as HttpUri;
use Common\Util\ResponseHelper;
use Common\Util\RestClient;
use Mockery as m;

/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */
class RestClientTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public $handleReponseMethods = array(
        'checkForValidResponseBody',
        'checkForInternalServerError',
        'checkForUnexpectedResponseCode'
    );

    public function getSutMock($methods = null)
    {
        if ($methods === null) {
            $methods = [];
        }

        return $this->createPartialMock(RestClient::class, $methods);
    }

    public function testUrl()
    {
        $mock = $this->getSutMock(array('pathOrParams'));
        $mock->expects($this->once())
            ->method('pathOrParams')
            ->with('/licence');
        $toString = $this->createPartialMock(HttpUri::class, array('toString'));
        $toString->expects($this->once())
            ->method('toString');
        $mock->url = $toString;
        $mock->url('/licence');
    }

    public function testCreate()
    {
        $mock = $this->getSutMock(array('post'));
        $mock->expects($this->once())
            ->method('post')
            ->with('/licence', array('id' => 7));
        $mock->create('/licence', array('id' => 7));
    }

    public function testPost()
    {
        $mock = $this->getSutMock(array('request'));

        $mock->expects($this->once())
            ->method('request')
            ->with('POST', null, array('id' => 7));

        $mock->post(null, array('id' => 7));
    }

    public function testRead()
    {
        $mock = $this->getSutMock(array('get'));

        $mock->expects($this->once())
            ->method('get')
            ->with(null, array('id' => 7));

        $mock->read(null, array('id' => 7));
    }

    public function testGet()
    {
        $mock = $this->getSutMock(array('request'));

        $mock->expects($this->once())
            ->method('request')
            ->with('GET', '/licence', array());

        $mock->get('licence', array());
    }

    public function testUpdate()
    {
        $mock = $this->getSutMock(array('put'));

        $mock->expects($this->once())
            ->method('put')
            ->with(null, array('id' => 7));

        $mock->update(null, array('id' => 7));
    }

    public function testPut()
    {
        $mock = $this->getSutMock(array('request'));

        $mock->expects($this->once())
            ->method('request')
            ->with('PUT', null, array('id' => 7));

        $mock->put(null, array('id' => 7));
    }

    public function testPatch()
    {
        $mock = $this->getSutMock(array('request'));

        $mock->expects($this->once())
            ->method('request')
            ->with('PATCH', null, array('id' => 7));

        $mock->patch(null, array('id' => 7));
    }

    public function testDelete()
    {
        $mock = $this->getSutMock(array('request'));

        $mock->expects($this->once())
            ->method('request')
            ->with('DELETE', null, array('id' => 7));

        $mock->delete(null, array('id' => 7));
    }

    public function testRequest()
    {
        $mock = $this->getSutMock(array('prepareRequest', 'getResponseHelper'));

        $mock->expects($this->once())
            ->method('prepareRequest')
            ->with('GET', 'licence', array('id' => 7));

        $httpResponse = m::mock(Response::class);

        $send = $this->createPartialMock(HttpClient::class, array('send'));
        $send->expects($this->once())
            ->method('send')
            ->will($this->returnValue($httpResponse));
        $mock->client = $send;

        $responseHelper = $this->createPartialMock(
            ResponseHelper::class,
            array('setMethod', 'setResponse', 'setParams', 'handleResponse')
        );
        $responseHelper->expects($this->once())
            ->method('setMethod')
            ->with('GET');
        $responseHelper->expects($this->once())
            ->method('setResponse')
            ->with($httpResponse);
        $responseHelper->expects($this->once())
            ->method('setParams')
            ->with(array('id' => 7));
        $responseHelper->expects($this->once())
            ->method('handleResponse');

        $mock->expects($this->once())
            ->method('getResponseHelper')
            ->will($this->returnValue($responseHelper));

        $mock->request('GET', 'licence', array('id' => 7));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testGetResponseHelper()
    {
        $mock = $this->getSutMock(null);
        $mock->getResponseHelper();
    }

    public function testPrepareRequest()
    {
        $mock = $this->getSutMock(array('getClientRequest', 'getAccept', 'getAcceptLanguage'));

        $accept = $this->createPartialMock(Accept::class, array('addMediaType'));
        $accept->expects($this->once())
            ->method('addMediaType')
            ->with('application/json');

        $mock->expects($this->once())
            ->method('getAccept')
            ->will($this->returnValue($accept));

        $acceptLanguage = $this->createPartialMock(AcceptLanguage::class, array('addLanguage'));
        $acceptLanguage->expects($this->once())
            ->method('addLanguage')
            ->with('en-gb');

        $mock->expects($this->once())
            ->method('getAcceptLanguage')
            ->will($this->returnValue($acceptLanguage));

        $client = $this->createPartialMock(
            HttpClient::class,
            array(
                'setRequest', 'setUri', 'setHeaders', 'setMethod', 'setEncType', 'setRawBody', 'getRequest',
                'resetParameters'
            )
        );

        $request = m::mock(Request::class);

        $client->expects($this->once())
            ->method('setRequest')
            ->with($request);
        $client->expects($this->once())
            ->method('setUri')
            ->with('licence');

        $toString = $this->createPartialMock(HttpUri::class, array('toString'));
        $toString->expects($this->once())
            ->method('toString');
        $mock->url = $toString;

        $client->expects($this->once())
            ->method('setHeaders');
        $client->expects($this->once())
            ->method('setMethod')
            ->with('POST');
        $client->expects($this->once())
            ->method('setEncType')
            ->with('application/json');
        $client->expects($this->once())
            ->method('setRawBody')
            ->with(json_encode(array('id' => 7)));
        $mock->client = $client;

        $mock->expects($this->once())
            ->method('getClientRequest')
            ->will($this->returnValue($request));

        $client->expects($this->once())
             ->method('resetParameters');

        $mock->prepareRequest('POST', 'licence', array('id' => 7));
    }

    /**
     * @NOTE I duplicate most of the above method just to get the coverage,
     *  These tests need attention, but that is out of scope in my story
     */
    public function testPrepareGetRequest()
    {
        $mock = $this->getSutMock(array('getClientRequest', 'getAccept', 'getAcceptLanguage'));

        $accept = $this->createPartialMock(Accept::class, array('addMediaType'));
        $accept->expects($this->once())
            ->method('addMediaType')
            ->with('application/json');

        $mock->expects($this->once())
            ->method('getAccept')
            ->will($this->returnValue($accept));

        $acceptLanguage = $this->createPartialMock(AcceptLanguage::class, array('addLanguage'));
        $acceptLanguage->expects($this->once())
            ->method('addLanguage')
            ->with('en-gb');

        $mock->expects($this->once())
            ->method('getAcceptLanguage')
            ->will($this->returnValue($acceptLanguage));

        $client = $this->createPartialMock(
            HttpClient::class,
            array(
                'setRequest',
                'setUri',
                'setHeaders',
                'setMethod',
                'getRequest',
                'resetParameters'
            )
        );

        $request = m::mock(Request::class);
        $parameters = m::mock(ParametersInterface::class);

        $client->expects($this->once())
            ->method('setRequest')
            ->with($request);
        $client->expects($this->once())
            ->method('setUri')
            ->with('licence');

        $toString = $this->createPartialMock(HttpUri::class, array('toString'));
        $toString->expects($this->once())
            ->method('toString');
        $mock->url = $toString;

        $client->expects($this->once())
            ->method('setHeaders');
        $client->expects($this->once())
            ->method('setMethod')
            ->with('GET');

        $client->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $request->expects('getQuery')->andReturn($parameters);
        $parameters->expects('fromArray')
            ->with(array('id' => 7));

        $mock->client = $client;

        $mock->expects($this->once())
            ->method('getClientRequest')
            ->will($this->returnValue($request));

        $mock->prepareRequest('GET', 'licence', array('id' => 7));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testGetAccept()
    {
        $mock = $this->getSutMock(null);
        $mock->getAccept();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testGetClientRequest()
    {
        $mock = $this->getSutMock(null);
        $mock->getClientRequest();
    }

    public function testGetLanguage()
    {
        $mock = $this->getSutMock(null);
        $this->assertEquals('en-gb', $mock->getLanguage());
    }

    public function testSetLanguage()
    {
        $mock = $this->getSutMock(null);
        $mock->setLanguage('cy_cy');
        $this->assertEquals('cy-cy', $mock->getLanguage());
    }

    public function testGetAcceptLanguage()
    {
        $sut = new RestClient(new HttpUri());
        $acceptLanguage = $sut->getAcceptLanguage();

        $this->assertInstanceOf('\Laminas\Http\Header\AcceptLanguage', $acceptLanguage);
    }

    public function testConstructorWithParams()
    {
        $options = [
            'foo' => 'bar',
        ];
        $auth = [
            'username' => 'test',
            'password' => 'secret',
        ];

        $sut = new RestClient(new HttpUri(), $options, $auth);

        $config = $sut->client->getAdapter()->getConfig();

        // check our config is set
        $this->assertEquals('bar', $config['foo']);

        // check previous default options are still set
        $this->assertTrue($config['keepalive']);
        $this->assertEquals(30, $config['timeout']);
    }
}
