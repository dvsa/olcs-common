<?php

namespace Common\Controller\Plugin;

use Common\Exception\BailOutException;
use Common\Exception\ResourceConflictException;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Helper\FlashMessengerHelperService;
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
     * @var FlashMessengerHelperService
     */
    private $fm;

    /**
     * @param TransferAnnotationBuilder $annotationBuilder
     * @param CommandService $commandService
     * @param FlashMessengerHelperService $fm
     */
    public function __construct(
        TransferAnnotationBuilder $annotationBuilder,
        CommandService $commandService,
        FlashMessengerHelperService $fm
    ) {
        $this->commandService = $commandService;
        $this->annotationBuilder = $annotationBuilder;
        $this->fm = $fm;
    }

    /**
     * @param CommandInterface $command
     * @return \Common\Service\Cqrs\Response
     */
    public function __invoke(CommandInterface $command)
    {
        $command = $this->annotationBuilder->createCommand($command);
        try {
            return $this->commandService->send($command);
        } catch (ResourceConflictException $ex) {
            $this->fm->addConflictError();
            throw new BailOutException('', $this->getController()->redirect()->refresh());
        }
    }
}
