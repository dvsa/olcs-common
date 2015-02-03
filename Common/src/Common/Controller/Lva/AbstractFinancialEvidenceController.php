<?php

/**
 * Financial Evidence Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Financial Evidence Trait
 *
 * @NOTE this is just a placeholder at the minute
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractFinancialEvidenceController extends AbstractController
{
    /**
     * Financial evidence section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();

            if (!isset($data['table']['action'])) {
                $this->postSave('financial_evidence');
                return $this->completeSection('financial_evidence');
            }
        }

        $form = $this->getFinancialEvidenceForm();

        $this->alterFormForLva($form);

        return $this->render(
            'financial_evidence',
            $form,
            [
                'vehicles' => 99,
                'trailers' => 88,
                'requiredFinance' => 12345.76,
            ]
        );
    }

    /**
     * Prepare the financial evidence form
     *
     * @return \Zend\Form\Form
     */
    private function getFinancialEvidenceForm()
    {
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\FinancialEvidence');

        return $form;
    }

    /**
     * For now the table data is stubbed data
     */
    private function getTableData()
    {
        return array(
            array(
                'id' => 1,
                'fileName' => 'Amber_taxis_accounts_2012-2013.xls',
                'type' => 'Accounts'
            )
        );
    }
}
