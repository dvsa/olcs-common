<?php

/**
 * Community Licence Document Generation Helper Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Helper;

use Common\Service\Entity\LicenceEntityService;
use Common\Service\Printing\PrintSchedulerInterface;

/**
 * Community Licence Document Generation Helper Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CommunityLicenceDocumentHelperService extends AbstractHelperService
{
    /**
     * Helper method to generate a document for an array of community licences
     *
     * @param array $licenceIds
     *
     */
    public function generateBatch($licenceIds = [])
    {
        foreach ($licenceIds as $id) {
            $licence = $this->getServiceLocator()
                ->get('Entity\CommunityLic')
                ->getWithLicence($id);

            $template = $this->getTemplateForLicence($licence['licence']);

            $query = [
                'licence' => $licence['licence']['id'],
                'communityLic' => $id
            ];

            $documentService = $this->getServiceLocator()
                ->get('Helper\DocumentGeneration');

            $document = $documentService->generateFromTemplate($template, $query);

            $file = $documentService->uploadGeneratedContent($document, 'documents', 'Community Licence');

            $this->getServiceLocator()->get('PrintScheduler')
                ->enqueueFile($file, 'Community Licence', [PrintSchedulerInterface::OPTION_DOUBLE_SIDED]);
        }
    }

    private function getTemplateForLicence($licence)
    {
        $prefix = '';

        if ($licence['licenceType']['id'] === LicenceEntityService::LICENCE_CATEGORY_PSV) {
            $prefix = 'PSV';
        } else if ($licence['niFlag'] === 'Y') {
            $prefix = 'GV_GB';
        } else {
            $prefix = 'GV_NI';
        }

        return $prefix . '_European_Community_Licence';
    }
}
