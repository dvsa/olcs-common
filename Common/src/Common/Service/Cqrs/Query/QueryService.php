<?php

/**
 * Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Query;

use Dvsa\Olcs\Transfer\Query\Query as DvsaQuery;
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

    public function __construct(RouteInterface $router, Client $client)
    {
        $this->router = $router;
        $this->client = $client;
    }

    /**
     * Send a query and return the response
     *
     * @param DvsaQuery $query
     * @return Response
     */
    public function send(DvsaQuery $query)
    {
        if (!$query->isValid()) {
            return $this->invalidResponse($query->getMessages(), HttpResponse::STATUS_CODE_422);
        }

        $routeName = $query->getRouteName();
        $data = $query->getDto()->getArrayCopy();

        try {
            $uri = $this->router->assemble($data, ['name' => 'api/' . $routeName . '/GET']);
        } catch (ExceptionInterface $ex) {
            return $this->invalidResponse([$ex->getMessage()], HttpResponse::STATUS_CODE_404);
        }

        $request = new Request();
        $request->setUri($uri);
        $request->setMethod('GET');

        try {
            return new Response($this->client->send($request));
        } catch (HttpClientExceptionInterface $ex) {
            return $this->invalidResponse([$ex->getMessage()], HttpResponse::STATUS_CODE_500);
        }
    }

    protected function invalidResponse(array $messages = [], $statusCode = HttpResponse::STATUS_CODE_500)
    {
        $response = new HttpResponse();
        $response->setStatusCode($statusCode);
        $response->setResult($messages);

        return new Response($response);
    }
}
