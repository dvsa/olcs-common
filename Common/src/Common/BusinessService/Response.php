<?php

/**
 * Response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService;

/**
 * Response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Response implements ResponseInterface
{
    protected $type = null;

    protected $data = null;

    protected $message = null;

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
