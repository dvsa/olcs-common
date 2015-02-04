<?php

/**
 * Abstract Financial Evidence Controller
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva;

/**
 * Abstract Financial Evidence Controller
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractFinancialEvidenceController extends AbstractController
{
    use Traits\AdapterAwareTrait;

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

        $this->getAdapter()->alterFormForLva($form);

        // set default value
        $form->get('evidence')->get('uploadNow')->setValue('Y');

        $id = $this->getIdentifier();

        $this->getServiceLocator()->get('Script')->loadFiles(['financial-evidence']);

        return $this->render(
            'financial_evidence',
            $form,
            [
                'vehicles' => $this->getAdapter()->getTotalNumberOfAuthorisedVehicles($id),
                'requiredFinance' => $this->getAdapter()->getRequiredFinance($id),
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
}
