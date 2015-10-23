<?php

/**
 * Command Sender Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Command;

/**
 * Command Sender Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait CommandSenderAwareTrait
{
    /**
     * @var CommandSender
     */
    protected $commandSender;

    /**
     * Set Command sender
     *
     * @param CommandSender $commandSender
     */
    public function setCommandSender(CommandSender $commandSender)
    {
        $this->commandSender = $commandSender;
    }

    /**
     * Get Command sender
     *
     * @return CommandSender
     */
    public function getCommandSender()
    {
        return $this->commandSender;
    }
}
