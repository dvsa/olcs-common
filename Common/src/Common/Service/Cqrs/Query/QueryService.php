<?php

/**
 * Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Query;

use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Common\Service\Cqrs\Response;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Router\RouteInterface;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Mvc\Router\Exception\ExceptionInterface;
use Zend\Http\Client\Exception\ExceptionInterface as HttpClientExceptionInterface;

/**
 * Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryService
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

    protected $cache;

    public function __construct(RouteInterface $router, Client $client, Request $request)
    {
        $this->router = $router;
        $this->client = $client;
        $this->request = $request;
    }

    /**
     * Send a query and return the response
     *
     * @param QueryContainerInterface $query
     * @return Response
     */
    public function send(QueryContainerInterface $query)
    {
        if (!$query->isValid()) {
            return $this->invalidResponse($query->getMessages(), HttpResponse::STATUS_CODE_422);
        }

        if ($query->isCachable()) {
            $id = $query->getCacheIdentifier();

            if (!isset($this->cache[$id])) {
                $this->cache[$id] = $this->handleSend($query);
            }

            return $this->cache[$id];
        }

        return $this->handleSend($query);
    }

    protected function handleSend(QueryContainerInterface $query)
    {
        $routeName = $query->getRouteName();
        $data = $query->getDto()->getArrayCopy();

        try {
            // @todo Tmp replace route name to prefix with api while we migrate all services
            $routeName = str_replace('backend/', 'backend/api/', $routeName);
            $uri = $this->router->assemble($data, ['name' => 'api/' . $routeName . '/GET']);
        } catch (ExceptionInterface $ex) {
            return $this->invalidResponse([$ex->getMessage()], HttpResponse::STATUS_CODE_404);
        }

        $this->request->setUri($uri);
        $this->request->setMethod('GET');

        try {
            return new Response($this->client->send($this->request));
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
