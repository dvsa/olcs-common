<?php

namespace Common\Controller\Plugin;

use Common\Service\Cqrs\Command\CommandService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Class HandleCommand
 * @package Common\Controller\Plugin
 */
class HandleCommand extends AbstractPlugin
{
    /**
     * @var TransferAnnotationBuilder
     */
    private $annotationBuilder;

    /**
     * @var CommandService
     */
    private $commandService;

    /**
     * @param TransferAnnotationBuilder $annotationBuilder
     * @param CommandService $commandService
     */
    public function __construct(TransferAnnotationBuilder $annotationBuilder, CommandService $commandService)
    {
        $this->commandService = $commandService;
        $this->annotationBuilder = $annotationBuilder;
    }

    /**
     * @param CommandInterface $command
     * @return \Common\Service\Cqrs\Response
     */
    public function __invoke(CommandInterface $command)
    {
        $command = $this->annotationBuilder->createCommand($command);
        return $this->commandService->send($command);
    }
}
