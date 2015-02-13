<?php

/**
 * Print Scheduler Interface
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Printing;

use Common\Service\File\File;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Print Scheduler Interface
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
interface PrintSchedulerInterface extends ServiceLocatorAwareInterface
{
    CONST OPTION_DOUBLE_SIDED = 1;

    public function enqueueFile(File $file, $jobName, $options = []);
}
