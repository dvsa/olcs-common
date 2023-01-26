<?php

namespace Common\Service\Cqrs\Command;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Interop\Container\ContainerInterface;

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

    public function createService(ServiceLocatorInterface $serviceLocator): CommandSender
    {
        return $this->__invoke($serviceLocator, CommandSender::class);
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

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CommandSender
    {
        $this->commandService = $container->get('CommandService');
        $this->annotationBuilder = $container->get('TransferAnnotationBuilder');
        return $this;
    }
}
