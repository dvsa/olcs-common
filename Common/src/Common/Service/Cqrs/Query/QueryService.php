<?php

/**
 * Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Query;

use Dvsa\Olcs\Transfer\Command\LoggerOmitContentInterface;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Common\Service\Cqrs\Response;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Router\RouteInterface;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Mvc\Router\Exception\ExceptionInterface;
use Zend\Http\Client\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Common\Service\Cqrs\CqrsTrait;

/**
 * Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryService implements QueryServiceInterface
{
    use CqrsTrait;

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

    public function __construct(
        RouteInterface $router,
        Client $client,
        Request $request,
        $showApiMessages,
        $flashMessenger
    ) {
        $this->router = $router;
        $this->client = $client;
        $this->request = $request;
        $this->showApiMessages = $showApiMessages;
        $this->flashMessenger = $flashMessenger;
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

        $adapter = $this->client->getAdapter();

        try {
            $this->client->resetParameters(true);

            if ($query->getDto() instanceof LoggerOmitContentInterface) {
                $shouldLogContent = $adapter->getShouldLogData();
                $adapter->setShouldLogData(false);
            }

            $clientResponse = $this->client->send($this->request);

            if ($query->getDto() instanceof LoggerOmitContentInterface) {
                $adapter->setShouldLogData($shouldLogContent);
            }

            $response = new Response($clientResponse);

            if ($this->showApiMessages) {
                $this->showApiMessagesFromResponse($response);
            }

            return $response;

        } catch (HttpClientExceptionInterface $ex) {
            return $this->invalidResponse([$ex->getMessage()], HttpResponse::STATUS_CODE_500);
        }
    }
}
