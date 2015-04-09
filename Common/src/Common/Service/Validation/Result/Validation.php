<?php

namespace Common\Service\Validation\Result;
use Common\Service\Validation\CommandInterface;

/**
 * Class Validation
 * @package Olcs\Ebsr\Data\Object\Result
 */
abstract class Validation
{
    /**
     * @var CommandInterface
     */
    protected $command;

    /**
     * @param $command
     */
    public function __construct(CommandInterface $command)
    {
        $this->command = $command;
    }

    /**
     * @return CommandInterface
     */
    public function getCommand()
    {
        return $this->command;
    }
}
