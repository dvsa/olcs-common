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
    protected $CommandSender;

    /**
     * Set Command sender
     *
     * @param CommandSender $CommandSender
     */
    public function setCommandSender(CommandSender $CommandSender)
    {
        $this->CommandSender = $CommandSender;
    }

    /**
     * Get Command sender
     *
     * @return CommandSender
     */
    public function getCommandSender()
    {
        return $this->CommandSender;
    }
}
