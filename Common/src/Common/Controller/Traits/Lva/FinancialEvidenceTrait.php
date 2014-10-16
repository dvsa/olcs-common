<?php

/**
 * Financial Evidence Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Financial Evidence Trait
 *
 * @NOTE this is just a placeholder at the minute
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait FinancialEvidenceTrait
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
                return $this->completeSection('financial_evidence');
            }
        }

        $form = $this->getFinancialEvidenceForm();

        return $this->render('financial_evidence', $form);
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

        $table = $this->getServiceLocator()
            ->get('Table')
            ->prepareTable('lva-financial-evidence', $this->getTableData());

        $form->get('table')->get('table')->setTable($table);

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
