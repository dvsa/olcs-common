<?php

/**
 * Document Stub Print Scheduler
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Printing;

use Common\Service\Data\CategoryDataService;
use Common\Service\File\File;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Document Stub Print Scheduler
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentStubPrintScheduler implements PrintSchedulerInterface
{
    use ServiceLocatorAwareTrait;

    public function enqueueFile(File $file, $jobName, $options = [])
    {
        $data = [
            'identifier'    => $file->getIdentifier(),
            'description'   => $jobName,
            'filename'      => str_replace(" ", "_", $jobName) . '.rtf',
            'fileExtension' => 'doc_rtf',
            'licence'       => 7, // hard coded simply so we can demo against *something*
            'category'      => CategoryDataService::CATEGORY_LICENSING,
            'subCategory'   => CategoryDataService::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isDigital'     => true,
            'isReadOnly'    => true,
            'issuedDate'    => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
            'size'          => $file->getSize()
        ];

        $this->getServiceLocator()
            ->get('Helper\Rest')
            ->makeRestCall(
                'Document',
                'POST',
                $data
            );
    }
}
