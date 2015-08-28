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
            'issuedDate' => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
            'category'    => CategoryDataService::CATEGORY_TRANSPORT_MANAGER,
            'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
        ];
    }

    public function alterResponsibilitiesFieldset($fieldset, $ocOptions, $otherLicencesTable)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $fieldset->get('operatingCentres')->setValueOptions($ocOptions);

        $formHelper->removeOption($fieldset->get('tmType'), 'tm_t_b');

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

    public function getConvictionsAndPenaltiesTable($transportManagerId)
    {
        $result = $this->handleQuery(
            \Dvsa\Olcs\Transfer\Query\PreviousConviction\GetList::create(['transportManager' => $transportManagerId])
        );
        $results = $result['results'];

        return $this->getServiceLocator()->get('Table')->prepareTable(
            'tm.convictionsandpenalties',
            $results
        );
    }

    /**
     * Execute a query DTO
     *
     * @param \Dvsa\Olcs\Transfer\Query\QueryInterface $dto
     *
     * @return array of results
     * @throws \RuntimeException
     */
    protected function handleQuery($dto)
    {
        $annotationBuilder = $this->getServiceLocator()->get('TransferAnnotationBuilder');
        $queryService = $this->getServiceLocator()->get('QueryService');
        $response = $queryService->send($annotationBuilder->createQuery($dto));

        if (!$response->isOk()) {
            throw new \RuntimeException('Error fetching query '. get_class($dto));
        }

        return $response->getResult();
    }


    public function getPreviousLicencesTable($transportManagerId)
    {
        $result = $this->handleQuery(
            \Dvsa\Olcs\Transfer\Query\OtherLicence\GetList::create(['transportManager' => $transportManagerId])
        );

        $results = $result['results'];

        return $this->getServiceLocator()->get('Table')->prepareTable(
            'tm.previouslicences',
            $results
        );
    }

    /**
     * This method superseeds alterPreviousHistoryFieldset
     *
     * @param \Zend\Form\Fieldset $fieldset
     * @param array               $tm
     */
    public function alterPreviousHistoryFieldsetTm($fieldset, $tm)
    {
        $convictionsAndPenaltiesTable = $this->getServiceLocator()->get('Table')->prepareTable(
            'tm.convictionsandpenalties',
            $tm['previousConvictions']
        );
        $previousLicencesTable = $this->getServiceLocator()->get('Table')->prepareTable(
            'tm.previouslicences',
            $tm['otherLicences']
        );

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

    public function alterPreviousHistoryFieldset($fieldset, $tmId)
    {
        $transportManager = $this->getTransportManager($tmId);
        $convictionsAndPenaltiesTable = $this->getConvictionsAndPenaltiesTable($transportManager['id']);
        $previousLicencesTable = $this->getPreviousLicencesTable($transportManager['id']);

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

        if (!is_null($transportManager['removedDate'])) {
            $fieldset->get('convictions')->get('table')->getTable()->setDisabled(true);
            $fieldset->get('previousLicences')->get('table')->getTable()->setDisabled(true);
        }
    }

    private function getTransportManager($tmId)
    {
        return $this->handleQuery(
            \Dvsa\Olcs\Transfer\Query\Tm\TransportManager::create(
                [
                    'id' => $tmId
                ]
            )
        );
    }

    /**
     * This method superseeds prepareOtherEmploymentTable
     *
     * @param \Zend\Form\Element $element
     * @param array              $tm      Transport Manager data
     */
    public function prepareOtherEmploymentTableTm($element, $tm)
    {
        $table = $this->getServiceLocator()->get('Table')->prepareTable('tm.employments', $tm['employments']);

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $formHelper->populateFormTable($element, $table, 'employment');
    }

    public function prepareOtherEmploymentTable($element, $tmId)
    {
        $table = $this->getOtherEmploymentTable($tmId);

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $formHelper->populateFormTable($element, $table, 'employment');
    }

    public function getOtherEmploymentTable($tmId)
    {
        $results = $this->getServiceLocator()->get('Entity\TmEmployment')->getAllEmploymentsForTm($tmId);

        return $this->getServiceLocator()->get('Table')->prepareTable('tm.employments', $results);
    }

    public function getOtherEmploymentData($id)
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(\Dvsa\Olcs\Transfer\Query\TmEmployment\GetSingle::create(['id' => $id]));
        /* @var $response \Common\Service\Cqrs\Response */
        $response = $this->getServiceLocator()->get('QueryService')->send($query);
        $employment = $response->getResult();

        $data = [
            'tm-employment-details' => [
                'id' => $employment['id'],
                'version' => $employment['version'],
                'position' => $employment['position'],
                'hoursPerWeek' => $employment['hoursPerWeek'],
            ],
            'tm-employer-name-details' => [
                'employerName' => $employment['employerName']
            ]
        ];

        if (isset($employment['contactDetails']['address'])) {
            $data['address'] = $employment['contactDetails']['address'];
        }

        return $data;
    }

    public function getReviewConfig($id)
    {
        $annotationBuilder = $this->getServiceLocator()->get('TransferAnnotationBuilder');
        $queryService = $this->getServiceLocator()->get('QueryService');

        $query = $annotationBuilder->createQuery(
            \Dvsa\Olcs\Transfer\Query\TransportManagerApplication\GetDetails::create(['id' => $id])
        );
        $response = $queryService->send($query);

        if (!$response->isOk()) {
            throw new \RuntimeException('Error getting Transport Manager Application review data');
        }
        $data = $response->getResult();

        $subTitle = sprintf(
            '%s %s/%s',
            $data['application']['licence']['organisation']['name'],
            $data['application']['licence']['licNo'],
            $data['application']['id']
        );

        $sections = [];

        $sections[] = $this->getMainDetailsReviewSection($data);
        $sections[] = $this->getResponsibilityDetailsReviewSection($data);
        $sections[] = $this->getOtherEmploymentDetailsReviewSection($data);
        $sections[] = $this->getPreviousConvictionDetailsReviewSection($data);
        $sections[] = $this->getPreviousLicenceDetailsReviewSection($data);

        return [
            'reviewTitle' => 'tm-review-title',
            'subTitle' => $subTitle,
            'settings' => [
                'hide-count' => true
            ],
            'sections' => $sections
        ];
    }

    protected function getMainDetailsReviewSection($data)
    {
        return [
            'header' => 'tm-review-main',
            'config' => $this->getServiceLocator()->get('Review\TransportManagerMain')->getConfigFromData($data)
        ];
    }

    protected function getResponsibilityDetailsReviewSection($data)
    {
        return [
            'header' => 'tm-review-responsibility',
            'config' => $this->getServiceLocator()->get('Review\TransportManagerResponsibility')
                ->getConfigFromData($data)
        ];
    }

    protected function getOtherEmploymentDetailsReviewSection($data)
    {
        return [
            'header' => 'tm-review-other-employment',
            'config' => $this->getServiceLocator()->get('Review\TransportManagerOtherEmployment')
                ->getConfigFromData($data)
        ];
    }

    protected function getPreviousConvictionDetailsReviewSection($data)
    {
        return [
            'header' => 'tm-review-previous-conviction',
            'config' => $this->getServiceLocator()->get('Review\TransportManagerPreviousConviction')
                ->getConfigFromData($data)
        ];
    }

    protected function getPreviousLicenceDetailsReviewSection($data)
    {
        return [
            'header' => 'tm-review-previous-licence',
            'config' => $this->getServiceLocator()->get('Review\TransportManagerPreviousLicence')
                ->getConfigFromData($data)
        ];
    }
}
