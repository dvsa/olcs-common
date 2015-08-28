<?php

/**
 * Command
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Command;

use Common\Exception\ResourceConflictException;
use Dvsa\Olcs\Transfer\Command\CommandContainerInterface;
use Common\Service\Cqrs\Response;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Router\RouteInterface;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Mvc\Router\Exception\ExceptionInterface;
use Zend\Http\Client\Exception\ExceptionInterface as HttpClientExceptionInterface;

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

    /**
     * @var Request
     */
    protected $request;

    public function __construct(RouteInterface $router, Client $client, Request $request)
    {
        $this->router = $router;
        $this->client = $client;
        $this->request = $request;
    }

    /**
     * Send a query and return the response
     *
     * @param DvsaCommand $command
     * @return Response
     */
    public function send(CommandContainerInterface $command)
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

        $this->request->setUri($uri);
        $this->request->setMethod($method);
        $this->request->setContent(json_encode($data));

        try {
            $clientResponse = $this->client->send($this->request);

            if ($clientResponse->getStatusCode() === \Zend\Http\Response::STATUS_CODE_409) {
                throw new ResourceConflictException('Resource conflict');
            }

            return new Response($clientResponse);
        } catch (HttpClientExceptionInterface $ex) {
            return $this->invalidResponse([$ex->getMessage()], HttpResponse::STATUS_CODE_500);
        }
    }

    protected function invalidResponse(array $messages = [], $statusCode = HttpResponse::STATUS_CODE_500)
    {
        $httpResponse = new HttpResponse();
        $httpResponse->setStatusCode($statusCode);
        $response = new Response($httpResponse);
        $response->setResult(['messages' => $messages]);

        return $response;
    }
}
