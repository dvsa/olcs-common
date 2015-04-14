<?php

namespace Common\Service\Validation\Result;
use Common\Service\Validation\CommandInterface;


/**
 * Class ValidationFailed
 * @package Common\Service\Validation\Result
 */
class ValidationFailed extends Validation
{
    /**
     * @var array
     */
    protected $messages;

    /**
     * @param $command
     * @param $messages
     */
    public function __construct(CommandInterface $command, $messages)
    {
        parent::__construct($command);
        $this->messages = $messages;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
