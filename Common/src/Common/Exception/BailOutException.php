<?php

/**
 * Bail Out Exception
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Exception;

/**
 * Bail Out Exception
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BailOutException extends \Exception
{
    protected $response;

    public function __construct($message, $response)
    {
        $this->message = $message;
        $this->response = $response;
    }

    /**
     * @return int
     */
    public function getResponse()
    {
        return $this->response;
    }
}
