<?php

/**
 * Response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs;

use Zend\Http\Response as HttpResponse;

/**
 * Response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Response
{
    protected $result;

    /**
     * @var HttpResponse
     */
    protected $httpResponse;

    public function __construct(HttpResponse $httpResponse)
    {
        $this->httpResponse = $httpResponse;
    }

    public function isClientError()
    {
        return $this->httpResponse->isClientError();
    }

    public function isNotFound()
    {
        return $this->httpResponse->isNotFound();
    }

    public function isOk()
    {
        return $this->httpResponse->isSuccess();
    }

    public function isServerError()
    {
        return $this->httpResponse->isServerError();
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        if ($this->result === null) {
            $this->result = json_decode($this->httpResponse->getBody(), true);
        }

        return $this->result;
    }
}
