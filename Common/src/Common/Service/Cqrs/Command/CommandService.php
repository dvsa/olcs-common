<?php

/**
 * Command
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Command;

use Common\Exception\ResourceConflictException;
use Common\Util\FileContent;
use Dvsa\Olcs\Transfer\Command\CommandContainerInterface;
use Common\Service\Cqrs\Response;
use Dvsa\Olcs\Transfer\Command\LoggerOmitContentInterface;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Zend\Http\Header\ContentType;
use Zend\Http\Headers;
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
     * @param CommandContainerInterface $command
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

        $this->client->resetParameters(true);
        $this->client->setRequest($this->request);

        $isMultipart = false;

        /**
         * Check for FileContent, then we will send as multipart rather than JSON
         */
        foreach ($data as $name => $value) {
            if ($value instanceof FileContent) {
                $isMultipart = true;
                $this->client->setFileUpload($value->getFileName(), $name);
            }
        }

        $this->request->setUri($uri);
        $this->request->setMethod($method);

        /**
         * If we are sending multipart, we need to remove the application/json header, the multipart header will be
         * added by ZF2s client
         */
        if ($isMultipart) {
            /**
             * Here we need to clone the request object, rather than update the current 1, as this service is a
             * singleton, and subsequent calls to ->send would result in the headers being incorrectly modified.
             */
            $request = clone $this->request;
            $headers = $request->getHeaders();
            $newHeaders = new Headers();
            foreach ($headers as $header) {
                if (!($header instanceof ContentType)) {
                    $newHeaders->addHeader($header);
                }
            }
            $request->setHeaders($newHeaders);
            $this->client->setRequest($request);
            $this->client->setParameterPost($data);
        } else {
            $this->request->setContent(json_encode($data));
        }

        /** @var ClientAdapterLoggingWrapper $adapter */
        $adapter = $this->client->getAdapter();

        try {
            if ($command->getDto() instanceof LoggerOmitContentInterface) {
                $shouldLogContent = $adapter->getShouldLogData();
                $adapter->setShouldLogData(false);
            }

            $clientResponse = $this->client->send();

            if ($command->getDto() instanceof LoggerOmitContentInterface) {
                $adapter->setShouldLogData($shouldLogContent);
            }

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
