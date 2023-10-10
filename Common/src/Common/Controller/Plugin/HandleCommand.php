<?php

namespace Common\Controller\Plugin;

use Common\Exception\BailOutException;
use Common\Exception\ResourceConflictException;
use Common\Service\Cqrs\Command\CommandSender;
use Common\Service\Helper\FlashMessengerHelperService;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class HandleCommand
 * @package Common\Controller\Plugin
 */
class HandleCommand extends AbstractPlugin
{

    /**
     * @var CommandSender
     */
    private $commandSender;

    /**
     * @var FlashMessengerHelperService
     */
    private $fm;

    /**
     * @param CommandSender $commandService
     * @param FlashMessengerHelperService $fm
     */
    public function __construct(CommandSender $sender, FlashMessengerHelperService $fm)
    {
        $this->commandSender = $sender;
        $this->fm = $fm;
    }

    /**
     * @param CommandInterface $command
     * @return \Common\Service\Cqrs\Response
     */
    public function __invoke(CommandInterface $command)
    {
        try {
            return $this->commandSender->send($command);
        } catch (ResourceConflictException $ex) {
            $this->fm->addConflictError();
            throw new BailOutException('', $this->getController()->redirect()->refresh());
        }
    }
}
