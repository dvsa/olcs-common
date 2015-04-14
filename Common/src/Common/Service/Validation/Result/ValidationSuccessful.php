<?php

namespace Common\Service\Validation\Result;
use Common\Service\Validation\CommandInterface;

/**
 * Class ValidationSuccessful
 * @package Common\Service\Validation\Result
 */
class ValidationSuccessful extends Validation
{
    /**
     * @var array
     */
    protected $result;

    /**
     * @var array
     */
    protected $context;

    /**
     * @param $command
     * @param $result
     * @param $context
     */
    public function __construct(CommandInterface $command, $result, $context = [])
    {
        parent::__construct($command);
        $this->result = $result;
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
