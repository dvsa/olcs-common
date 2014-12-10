<?php

namespace Common\Util;

use Zend\Http\Response;

/**
 * Class MultiResponseHelper
 * @package Common\Util
 */
class MultiResponseHelper extends ResponseHelper
{
    /**
     * @return array|bool
     */
    public function handleResponse()
    {
        $this->body = $this->response->getBody();
        $this->checkForValidResponseBody($this->body);
        $this->checkForInternalServerError($this->body);

        if ($this->method == 'POST' && $this->response->getStatusCode() == Response::STATUS_CODE_207) {
            //response is a multi status response
            $responses = $this->responseData['Data'];
            $return = [];
            foreach ($responses as $uri => $response) {
                //could add code to check for bad statuses, but for EBSR the 400 response is used for validation
                //errors so shouldn't trigger an exception
                $return[$uri] = $response['Data'];
            }

            if (count($return)) {
                return $return;
            }
        } else {
            $this->checkForUnexpectedResponseCode($this->body);
            return $this->processResponse();
        }

        return false;
    }
}
