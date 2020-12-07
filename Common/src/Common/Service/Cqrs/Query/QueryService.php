<?php

namespace Common\Service\Cqrs\Query;

use Common\Service\Cqrs\CqrsTrait;
use Common\Service\Cqrs\Exception;
use Common\Service\Cqrs\Response;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Cqrs\RecoverHttpClientExceptionTrait;
use Dvsa\Olcs\Transfer\Query\LoggerOmitResponseInterface;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\Http\Client;
use Laminas\Http\Client\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Laminas\Http\Request;
use Laminas\Http\Response as HttpResponse;
use Laminas\Mvc\Router\Exception\ExceptionInterface;
use Laminas\Mvc\Router\RouteInterface;

/**
 * Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryService implements QueryServiceInterface
{
    use CqrsTrait;
    use RecoverHttpClientExceptionTrait;

    /** @var RouteInterface */
    protected $router;
    /** @var Client */
    protected $client;
    /** @var Request */
    protected $request;

    /**
     * QueryService constructor.
     *
     * @param RouteInterface              $router          Router
     * @param Client                      $client          Http Client
     * @param Request                     $request         Http Request
     * @param boolean                     $showApiMessages Is Show Api Messages
     * @param FlashMessengerHelperService $flashMessenger  Flash messeger service
     *
     * @return void
     */
    public function __construct(
        RouteInterface $router,
        Client $client,
        Request $request,
        $showApiMessages,
        FlashMessengerHelperService $flashMessenger
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
     * @param QueryContainerInterface $query Query container
     *
     * @return Response
     */
    public function send(QueryContainerInterface $query)
    {
        if (!$query->isValid()) {
            return $this->invalidResponse($query->getMessages(), HttpResponse::STATUS_CODE_422);
        }

        $routeName = $query->getRouteName();

        /** @var QueryInterface $queryDto */
        $queryDto = $query->getDto();

        try {
            // @todo Tmp replace route name to prefix with api while we migrate all services
            $routeName = str_replace('backend/', 'backend/api/', $routeName);
            $uri = $this->router->assemble(
                $queryDto->getArrayCopy(),
                ['name' => 'api/' . $routeName . '/GET']
            );
        } catch (ExceptionInterface $ex) {
            throw new Exception($ex->getMessage(), HttpResponse::STATUS_CODE_404, $ex);
        }

        $this->request->setUri($uri);
        $this->request->setMethod(Request::METHOD_GET);

        /** @var \Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper $adapter */
        $adapter = $this->client->getAdapter();

        try {
            $this->client->resetParameters(true);

            $shouldLogContent = true;
            $isOmitLog = ($queryDto instanceof LoggerOmitResponseInterface);

            if ($isOmitLog) {
                $shouldLogContent = $adapter->getShouldLogData();
                $adapter->setShouldLogData(false);
            }

            //  request should use stream for query or reset
            $this->client->setStream($query->isStream());

            $clientResponse = $this->client->send($this->request);

            if ($isOmitLog) {
                $adapter->setShouldLogData($shouldLogContent);
            }

            $response = new Response($clientResponse);

            if ($response->getStatusCode() === HttpResponse::STATUS_CODE_404) {
                throw new Exception\NotFoundException('API responded with a 404 Not Found : '. $uri);
            }
            if ($response->getStatusCode() === HttpResponse::STATUS_CODE_403) {
                throw new Exception\AccessDeniedException($response->getBody() ." : ". $uri);
            }
            if ($response->getStatusCode() > HttpResponse::STATUS_CODE_400) {
                throw new Exception($response->getBody()  .' : '. $uri);
            }

            if ($this->showApiMessages) {
                $this->showApiMessagesFromResponse($response);
            }

            return $response;
        } catch (HttpClientExceptionInterface $ex) {
            if ($this->getRecoverHttpClientException()) {
                return new Response((new HttpResponse())->setStatusCode(HttpResponse::STATUS_CODE_500));
            }
            throw new Exception($ex->getMessage(), HttpResponse::STATUS_CODE_500, $ex);
        }
    }
}
