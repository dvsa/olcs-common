<?php

/**
 * Print Licence Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Processing;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Service\Entity\LicenceEntityService;

/**
 * Print Licence Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PrintLicenceProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function printLicence($licenceId)
    {
        $licence = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getOverview($licenceId);

        $prefix = $licence['niFlag'] === 'N' ? 'GB' : 'NI';
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
    }

    private function getTemplateName($licence)
    {
        if ($licence['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            return 'GV_LICENCE_V1';
        }

        if ($licence['licenceType']['id'] === LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            return 'PSV_SRLicence';
        }

        return 'PSV_LICENCE_V1';
    }

    private function getDescription($licence)
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
