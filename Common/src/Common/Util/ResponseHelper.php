<?php

/**
 * Response Helper
 *
 * Handle responses from the rest service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Util;

use Zend\Http\Response;

/**
 * Response Helper
 *
 * Handle responses from the rest service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ResponseHelper
{
    public $response;

    public $method;

    private $responseData;

    private $params;

    private $data;

    private $expectedCodes = array(
        'GET' => array(
            Response::STATUS_CODE_200,
            Response::STATUS_CODE_404
        ),
        'POST' => array(
            Response::STATUS_CODE_201,
            Response::STATUS_CODE_400
        ),
        'PUT' => array(
            Response::STATUS_CODE_200,
            Response::STATUS_CODE_400,
            Response::STATUS_CODE_404,
            Response::STATUS_CODE_409
        ),
        'PATCH' => array(
            Response::STATUS_CODE_200,
            Response::STATUS_CODE_400,
            Response::STATUS_CODE_404,
            Response::STATUS_CODE_409
        ),
        'DELETE' => array(
            Response::STATUS_CODE_200,
            Response::STATUS_CODE_404
        )
    );

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getData()
    {
        return $this->data;
    }

    public function handleResponse()
    {
        $this->body = $this->response->getBody();

        $this->checkForValidResponseBody($this->body);

        $this->checkForInternalServerError($this->body);

        $this->checkForUnexpectedResponseCode($this->body);

        switch ($this->method) {
            case 'GET':

                if ($this->response->getStatusCode() === Response::STATUS_CODE_200) {

                    return isset($this->responseData['Data']) ? $this->responseData['Data'] : $this->responseData;
                }

                return false;
            case 'POST':

                if ($this->response->getStatusCode() === Response::STATUS_CODE_201) {

                    return $this->responseData['Data'];
                }

                return false;
            // These currently do the same thing
            case 'PUT':
            case 'PATCH':

                if ($this->response->getStatusCode() === Response::STATUS_CODE_200) {

                    return $this->responseData['Data'];
                }

                return $this->response->getStatusCode();
            case 'DELETE':

                if ($this->response->getStatusCode() === Response::STATUS_CODE_200) {

                    return $this->responseData['Data'];
                }

                return false;
        }
    }

    public function checkForValidResponseBody($body)
    {
        if (!is_string($body)) {
            throw new \Exception('Invalid response body, expected string' . $body);
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {

            throw new \Exception('Invalid response body, expected json: ' . $body);
        }

        $this->responseData = (isset($data['Response']) ? $data['Response'] : $data);
    }

    public function checkForInternalServerError($body)
    {
        if ($this->response->getStatusCode() == Response::STATUS_CODE_500) {

            $data = json_decode($body, true);

            if (json_last_error() == JSON_ERROR_NONE) {
                $body = "\n" . print_r($data, true);
            }

            throw new \Exception('Internal server error: ' . $body);
        }
    }

    public function checkForUnexpectedResponseCode($body)
    {
        if (!in_array($this->response->getStatusCode(), $this->expectedCodes[$this->method])) {

            $data = json_decode($body, true);

            if (json_last_error() == JSON_ERROR_NONE) {
                $body = "\n" . print_r($data, true);
            }

            // TODO: Replace with a different exception
            throw new \Exception('Unexpected status code: ' . $body);
        }
    }
}
