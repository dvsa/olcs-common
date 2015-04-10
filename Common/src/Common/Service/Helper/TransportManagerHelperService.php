<?php

/**
 * Transport Manager Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Common\Service\Data\CategoryDataService;

/**
 * Transport Manager Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerHelperService extends AbstractHelperService
{
    public function getCertificateFiles($tmId)
    {
        return $this->getServiceLocator()->get('Entity\TransportManager')
            ->getDocuments(
                $tmId,
                null,
                null,
                CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
                CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
            );
    }

    public function getCertificateFileData($tmId, $file)
    {
        return [
            'transportManager' => $tmId,
            'description' => $file['name'],
            'category'    => CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
            'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
        ];
    }

    public function alterResponsibilitiesFieldset($fieldset, $ocOptions, $otherLicencesTable)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $fieldset->get('operatingCentres')->setValueOptions($ocOptions);

        $formHelper->removeOption($fieldset->get('tmType'), 'tm_t_B');

        $formHelper->populateFormTable($fieldset->get('otherLicences'), $otherLicencesTable);
    }

    public function getResponsibilityFileData($tmId)
    {
        return [
            'transportManager' => $tmId,
            'issuedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
            'description' => 'Additional information',
            'category'    => CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
            'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL
        ];
    }

    /**
     * Get transport manager documents
     *
     * @return array
     */
    public function getResponsibilityFiles($tmId, $tmaId)
    {
        $data = $this->getServiceLocator()->get('Entity\TransportManagerApplication')
            ->getTransportManagerApplication($tmaId);

        return $this->getServiceLocator()->get('Entity\TransportManager')
            ->getDocuments(
                $tmId,
                $data['application']['id'],
                'application',
                CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
                CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL
            );
    }
}
