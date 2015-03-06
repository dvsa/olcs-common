<?php

/**
 * Application Financial Evidence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

/**
 * Application Financial Evidence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationFinancialEvidenceReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $financialEvidenceAdapter = $this->getServiceLocator()->get('ApplicationFinancialEvidenceAdapter');

        return [
            'multiItems' => [
                [
                    [
                        'label' => 'application-review-financial-evidence-no-of-vehicles',
                        'value' => $financialEvidenceAdapter->getTotalNumberOfAuthorisedVehicles($data['id'])
                    ],
                    [
                        'label' => 'application-review-financial-evidence-required-finance',
                        'value' => $this->formatAmount($financialEvidenceAdapter->getRequiredFinance($data['id']))
                    ],
                    [
                        'label' => 'application-review-financial-evidence-evidence',
                        'noEscape' => true,
                        'value' => $this->getEvidence($data, $financialEvidenceAdapter)
                    ]
                ]
            ]
        ];
    }

    private function getEvidence($data, $financialEvidenceAdapter)
    {
        if ($data['financialEvidenceUploaded'] === 'N') {
            return $this->translate('application-review-financial-evidence-evidence-post');
        }

        $documents = $financialEvidenceAdapter->getDocuments($data['id']);

        return $this->formatDocumentList($documents);
    }

    private function formatDocumentList($documents)
    {
        $files = [];

        foreach ($documents as $document) {
            $files[] = $document['filename'];
        }

        return implode('<br>', $files);
    }
}
