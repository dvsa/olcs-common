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
            'issuedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate(\DateTime::W3C),
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

    public function getConvictionsAndPenaltiesTable($transportManagerId)
    {
        $results = $this->getServiceLocator()
            ->get('Entity\PreviousConviction')
            ->getDataForTransportManager($transportManagerId);

        return $this->getServiceLocator()->get('Table')->prepareTable(
            'tm.convictionsandpenalties',
            $results
        );
    }

    public function getPreviousLicencesTable($transportManagerId)
    {
        $results = $this->getServiceLocator()
            ->get('Entity\OtherLicence')
            ->getDataForTransportManager($transportManagerId);

        return $this->getServiceLocator()->get('Table')->prepareTable(
            'tm.previouslicences',
            $results
        );
    }

    public function alterPreviousHistoryFieldset($fieldset, $tmId)
    {
        $convictionsAndPenaltiesTable = $this->getConvictionsAndPenaltiesTable($tmId);
        $previousLicencesTable = $this->getPreviousLicencesTable($tmId);

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $formHelper->populateFormTable(
            $fieldset->get('convictions'),
            $convictionsAndPenaltiesTable,
            'convictions'
        );
        $formHelper->populateFormTable(
            $fieldset->get('previousLicences'),
            $previousLicencesTable,
            'previousLicences'
        );
    }
}
