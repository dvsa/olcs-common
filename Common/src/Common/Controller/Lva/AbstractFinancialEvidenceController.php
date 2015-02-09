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

        // we need to show rates in the 'help' section of the form/view
        // @TODO, show PSV as well?
        $standardFirst = $this->getAdapter()->getFirstVehicleRate(
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE
        );
        $standardAdditional = $this->getAdapter()->getAdditionalVehicleRate(
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE
        );
        $restrictedFirst = $this->getAdapter()->getFirstVehicleRate(
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE
        );
        $restrictedAdditional = $this->getAdapter()->getAdditionalVehicleRate(
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE
        );
        return $this->render(
            'financial_evidence',
            $form,
            [
                'vehicles' => $this->getAdapter()->getTotalNumberOfAuthorisedVehicles($id),
                'requiredFinance' => $this->getAdapter()->getRequiredFinance($id),
                'standardFirst' => $standardFirst,
                'standardAdditional' => $standardAdditional,
                'restrictedFirst' => $restrictedFirst,
                'restrictedAdditional' => $restrictedAdditional,
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
