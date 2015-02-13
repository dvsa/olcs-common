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
    public function generateBatch($licenceId, $communityLicenceIds = [])
    {
        // we need this because we're interested in its category & type
        $licence = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getOverview($licenceId);

        foreach ($communityLicenceIds as $id) {
            $template = $this->getTemplateForLicence($licence);

            $query = [
                'licence' => $licenceId,
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
        } elseif ($licence['niFlag'] === 'Y') {
            $prefix = 'GV_NI';
        } else {
            $prefix = 'GV_GB';
        }

        return $prefix . '_European_Community_Licence';
    }
}
