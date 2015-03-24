<?php

/**
 * Licence Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Processing;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Data\CategoryDataService;

/**
 * Licence Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function generateDocument($licenceId)
    {
        $licence = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getOverview($licenceId);

        $prefix = $this->getPrefix($licence);

        $template = $prefix . '/' . $this->getTemplateName($licence);

        $description = $this->getDescription($licence);

        $content = $this->getServiceLocator()
            ->get('Helper\DocumentGeneration')
            ->generateFromTemplate($template, ['licence' => $licenceId]);

        $storedFile = $this->getServiceLocator()
            ->get('Helper\DocumentGeneration')
            ->uploadGeneratedContent($content, 'documents', $description);

        $this->getServiceLocator()
            ->get('PrintScheduler')
            ->enqueueFile($storedFile, $description);

        $this->getServiceLocator()->get('Entity\Document')->createFromFile(
            $storedFile,
            [
                'description'   => $description,
                'filename'      => str_replace(" ", "_", $description) . '.rtf',
                'fileExtension' => 'doc_rtf',
                'licence'       => $licenceId,
                'category'      => CategoryDataService::CATEGORY_LICENSING,
                'subCategory'   => CategoryDataService::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
                'isReadOnly'    => true
            ]
        );
    }

    public function generateInterimDocument($applicationId)
    {
        $application = $this->getServiceLocator()
            ->get('Entity\Application')
            ->getDataForProcessing($applicationId);

        $licenceId = $application['licence']['id'];

        $prefix = $this->getPrefix($application);

        switch($application['isVariation']) {
            case true:
                $template = $prefix . '/' . 'GV_INT_DIRECTION_V1';
                $description = "GV Interim Direction";
                break;
            case false:
                $template = $prefix . '/' . 'GV_INT_LICENCE';
                $description = "GV Interim Licence";
                break;
        }

        $content = $this->getServiceLocator()
            ->get('Helper\DocumentGeneration')
            ->generateFromTemplate($template, ['licence' => $licenceId]);

        $storedFile = $this->getServiceLocator()
            ->get('Helper\DocumentGeneration')
            ->uploadGeneratedContent($content, 'documents', $description);

        $this->getServiceLocator()
            ->get('PrintScheduler')
            ->enqueueFile($storedFile, $description);

        $this->getServiceLocator()->get('Entity\Document')->createFromFile(
            $storedFile,
            [
                'description'   => $description,
                'filename'      => str_replace(" ", "_", $description) . '.rtf',
                'application'   => $applicationId,
                'licence'       => $licenceId,
                'fileExtension' => 'doc_rtf',
                'category'      => CategoryDataService::CATEGORY_LICENSING,
                'subCategory'   => CategoryDataService::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
                'isDigital'     => false,
                'isScan'        => false
            ]
        );
    }

    private function getPrefix(array $licence)
    {
        return $licence['niFlag'] === 'N' ? 'GB' : 'NI';
    }

    private function getTemplateName(array $licence)
    {
        if ($licence['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            return 'GV_LICENCE_V1';
        }

        if ($licence['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            return 'PSVSRLicence';
        }

        return 'PSV_LICENCE_V1';
    }

    private function getDescription(array $licence)
    {
        if ($licence['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            return 'GV Licence';
        }

        if ($licence['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            return 'PSV-SR Licence';
        }

        return 'PSV Licence';
    }
}
