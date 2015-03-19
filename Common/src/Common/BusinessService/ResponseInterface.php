<?php

/**
 * Response Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService;

/**
 * Response Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface ResponseInterface
{
    // These are currently the only response types I can think of for now, please add others if required
    const TYPE_PERSIST_SUCCESS = 1;
    const TYPE_PERSIST_FAILED = 2;
    const TYPE_RULE_FAILED = 3;
    const TYPE_NO_OP = 4;

    public function setType($type);

    public function getType();

    public function setData(array $data);

    public function getData();

    public function setMessage($message);

    public function getMessage();
}
