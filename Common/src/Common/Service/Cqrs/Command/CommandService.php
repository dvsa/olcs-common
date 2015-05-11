<?php

/**
 * Command
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Command;

use Dvsa\Olcs\Transfer\Command\Command as DvsaCommand;
use Common\Service\Cqrs\Response;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Router\RouteInterface;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Mvc\Router\Exception\ExceptionInterface;
use Zend\Http\Client\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Zend\Http\Headers;
use Zend\Http\Header\Accept;
use Zend\Http\Header\ContentType;

/**
 * Command
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandService
{
    /**
     * @var RouteInterface
     */
    protected $router;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(RouteInterface $router, Client $client)
    {
        $this->router = $router;
        $this->client = $client;
    }

    /**
     * Send a query and return the response
     *
     * @param DvsaCommand $command
     * @return Response
     */
    public function send(DvsaCommand $command)
    {
        if (!$command->isValid()) {
            return $this->invalidResponse($command->getMessages(), HttpResponse::STATUS_CODE_422);
        }

        $routeName = $command->getRouteName();
        $method = $command->getMethod();
        $data = $command->getDto()->getArrayCopy();

        try {
            // @todo Tmp replace route name to prefix with api while we migrate all services
            $routeName = str_replace('backend/', 'backend/api/', $routeName);
            $uri = $this->router->assemble($data, ['name' => 'api/' . $routeName . '/' . $method]);
        } catch (ExceptionInterface $ex) {
            return $this->invalidResponse([$ex->getMessage()], HttpResponse::STATUS_CODE_404);
        }

        $accept = new Accept();
        $accept->addMediaType('application/json');

        $contentType = new ContentType();
        $contentType->setMediaType('application/json');

        $headers = new Headers();
        $headers->addHeaders([$accept, $contentType]);

        $request = new Request();
        $request->setUri($uri);
        $request->setMethod($method);
        $request->setContent(json_encode($data));
        $request->setHeaders($headers);

        try {
            return new Response($this->client->send($request));
        } catch (HttpClientExceptionInterface $ex) {
            return $this->invalidResponse([$ex->getMessage()], HttpResponse::STATUS_CODE_500);
        }
    }

    protected function invalidResponse(array $messages = [], $statusCode = HttpResponse::STATUS_CODE_500)
    {
        $httpResponse = new HttpResponse();
        $httpResponse->setStatusCode($statusCode);
        $response = new Response($httpResponse);
        $response->setResult($messages);

        return $response;
    }
}
