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

        $template = $this->getTemplateName($licence);

        $description = $this->getDescription($licence);

        $storedFile = $this->getServiceLocator()
            ->get('Helper\DocumentGeneration')
            ->generateAndStore($template, $description, ['licence' => $licenceId]);

        $this->getServiceLocator()
            ->get('PrintScheduler')
            ->enqueueFile($storedFile, $description);

        $this->getServiceLocator()->get('Entity\Document')->createFromFile(
            $storedFile,
            [
                'description' => $description,
                'filename'    => str_replace(" ", "_", $description) . '.rtf',
                'licence'     => $licenceId,
                'category'    => CategoryDataService::CATEGORY_LICENSING,
                'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
                'isReadOnly'  => true,
                'isExternal'  => false
            ]
        );
    }

    public function generateInterimDocument($applicationId)
    {
        $application = $this->getServiceLocator()
            ->get('Entity\Application')
            ->getDataForProcessing($applicationId);

        $licenceId = $application['licence']['id'];

        if ($application['isVariation']) {
            $template = 'GV_INT_DIRECTION_V1';
            $description = "GV Interim Direction";
        } else {
            $template = 'GV_INT_LICENCE_V1';
            $description = "GV Interim Licence";
        }

        $storedFile = $this->getServiceLocator()
            ->get('Helper\DocumentGeneration')
            ->generateAndStore(
                $template,
                $description,
                [
                    'application' => $applicationId,
                    'licence' => $licenceId
                ]
            );

        $this->getServiceLocator()
            ->get('PrintScheduler')
            ->enqueueFile($storedFile, $description);

        $this->getServiceLocator()->get('Entity\Document')->createFromFile(
            $storedFile,
            [
                'description' => $description,
                'filename'    => str_replace(" ", "_", $description) . '.rtf',
                'application' => $applicationId,
                'licence'     => $licenceId,
                'category'    => CategoryDataService::CATEGORY_LICENSING,
                'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
                'isExternal'  => false,
                'isScan'      => false
            ]
        );
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
