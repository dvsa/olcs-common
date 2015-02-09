<?php

/**
 * Abstract Financial Evidence Controller
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva;

use Common\Service\Entity\LicenceEntityService as Licence;

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

        $id = $this->getIdentifier();

        $this->getServiceLocator()->get('Script')->loadFiles(['financial-evidence']);

        $variables = array_merge(
            [
                'vehicles' => $this->getAdapter()->getTotalNumberOfAuthorisedVehicles($id),
                'requiredFinance' => $this->getAdapter()->getRequiredFinance($id),
            ],
            $this->getAdapter()->getRatesForView()
        );
        return $this->render(
            'financial_evidence',
            $form,
            $variables
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
