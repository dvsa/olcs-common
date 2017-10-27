<?php

namespace Common\FormService\Form\Lva;

use Common\Form\Elements\Types\HtmlTranslated;
use Common\FormService\Form\AbstractFormService;
use Common\Form\Form;
use Common\RefData;
use Zend\Form\ElementInterface;
use Zend\Form\FieldsetInterface;
use Zend\Http\Request;

/**
 * FinancialHistory Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialHistory extends AbstractFormService
{
    /**
     * Get form
     *
     * @param Request $request Request
     * @param array   $data    Data for form
     *
     * @return Form
     */
    public function getForm($request, array $data = [])
    {
        /** @var Form $form */
        $form = $this->getFormHelper()->createFormWithRequest('Lva\FinancialHistory', $request);

        $this->alterForm($form, $data);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param Form  $form Form
     * @param array $data Data for form
     *
     * @return Form
     */
    protected function alterForm(Form $form, array $data = [])
    {
        if (isset($data['lva']) && in_array($data['lva'], ['variation', 'application'])) {
            $this->updateInsolvencyConfirmationLabel($form, $data);
        }

        if (isset($data['variationType']) && $data['variationType'] == RefData::VARIATION_TYPE_DIRECTOR_CHANGE) {
            $this->getFormHelper()->remove($form, 'data->financeHint');

            /** @var FieldsetInterface $dataFieldset */
            $dataFieldset = $form->get('data');

            /** @var HtmlTranslated $hasAnyPerson */
            $hasAnyPerson = $dataFieldset->get('hasAnyPerson');

            $hasAnyPerson->setTokens(
                [sprintf('Have any of the new %s been:', $this->getPersonDescription($data['organisationType']))]
            );
        }

        return $form;
    }

    /**
     * If the licence is NI then update the label.  Used in current controller
     * and CommonVariationControllerTrait.
     *
     * @param Form  $form Form
     * @param array $data Api/Form Data
     *
     * @return Form
     */
    protected function updateInsolvencyConfirmationLabel(Form $form, $data = null)
    {
        if (isset($data['niFlag']) && $data['niFlag'] === 'Y') {
            /** @var FieldsetInterface $dataFieldset */
            $dataFieldset = $form->get('data');
            /** @var FieldsetInterface $financialHistoryConfirmationFieldset */
            $financialHistoryConfirmationFieldset = $dataFieldset->get('financialHistoryConfirmation');
            /** @var ElementInterface $insolvencyConfirmationField */
            $insolvencyConfirmationField = $financialHistoryConfirmationFieldset
                ->get('insolvencyConfirmation');
            $insolvencyConfirmationField->setLabel(
                'application_previous-history_financial-history.insolvencyConfirmation.title.ni'
            );
        }

        return $form;
    }

    /**
     * Get a word to refer to people in charge of the organisation
     *
     * @param $organisationType
     *
     * @return string
     */
    private function getPersonDescription($organisationType)
    {
        switch ($organisationType) {
            case RefData::ORG_TYPE_REGISTERED_COMPANY:
                return 'directors';
            case RefData::ORG_TYPE_LLP:
            case RefData::ORG_TYPE_PARTNERSHIP:
                return 'partners';
            default:
                return 'people';
        }
    }
}
