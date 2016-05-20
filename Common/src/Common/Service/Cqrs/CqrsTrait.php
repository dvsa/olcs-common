<?php

/**
 * Cqrs Trait
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Cqrs;

use Zend\Http\Response as HttpResponse;

/**
 * Cqrs Trait
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
trait CqrsTrait
{
    /**
     * @var bool
     */
    protected $showApiMessages;

    /**
     * @var \Common\Service\Helper\FlashMessengerHelperService
     */
    protected $flashMessenger;

    /**
     * Invalid response
     *
     * @param array $messages
     * @param int $statusCode
     * @return Response
     */
    protected function invalidResponse(array $messages = [], $statusCode = HttpResponse::STATUS_CODE_500)
    {
        $httpResponse = new HttpResponse();
        $httpResponse->setStatusCode($statusCode);
        $response = new Response($httpResponse);
        $response->setResult(['messages' => $messages]);

        if ($this->showApiMessages) {
            $this->showApiMessages($messages);
        }

        return $response;
    }

    /**
     * Show API messages
     *
     * @param array $messages
     */
    protected function showApiMessages($messages = [])
    {
        foreach ($messages as $message) {
            $this->flashMessenger->addErrorMessage($message);
        }
    }

    /**
     * Show API messages from response
     *
     * @param \Common\Service\Cqrs\Response $response
     */
    protected function showApiMessagesFromResponse($response)
    {
        $result = $response->getResult();

        if (json_last_error()) {
            $this->showApiMessages(['Error decoding json response: ' . $response->getBody()]);
        }

        if (!$response->isOk() && isset($result['messages'])) {
            $this->showApiMessages($result['messages']);
        }
    }
}
