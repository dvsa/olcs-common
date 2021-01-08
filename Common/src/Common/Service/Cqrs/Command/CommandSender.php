<?php

namespace Common\Service\Cqrs\Command;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;

/**
 * Command Sender
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandSender implements FactoryInterface
{
    /**
     * @var TransferAnnotationBuilder
     */
    private $annotationBuilder;

    /**
     * @var CommandService
     */
    private $commandService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->commandService = $serviceLocator->get('CommandService');
        $this->annotationBuilder = $serviceLocator->get('TransferAnnotationBuilder');

        return $this;
    }

    /**
     * @param CommandInterface $command
     * @return \Common\Service\Cqrs\Response
     */
    public function send(CommandInterface $command)
    {
        $command = $this->annotationBuilder->createCommand($command);
        return $this->commandService->send($command);
    }
}
