<?php

/**
 * Application Snapshot Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Processing;

use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Dvsa\Olcs\Transfer\Query\Application\Application;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Application Snapshot Processing Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationSnapshotProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function storeSnapshot($applicationId, $event)
    {
        $data = ['id' => $applicationId, 'event' => $event];
        $annotationBuilder = $this->getServiceLocator()->get('TransferAnnotationBuilder');
        $command = $annotationBuilder->createCommand(CreateSnapshot::create($data));
        $commandService = $this->getServiceLocator()->get('CommandService');
        $commandService->send($command);
    }
}
