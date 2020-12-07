<?php

namespace Common\FormService\Form\Lva;

/**
 * FinancialEvidence Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialEvidence extends AbstractLvaFormService
{
    /**
     * Get Form
     *
     * @param \Laminas\Http\Request $request Request
     *
     * @return \Laminas\Form\Form
     */
    public function getForm(\Laminas\Http\Request $request)
    {
        $form = $this->getFormHelper()->createFormWithRequest('Lva\FinancialEvidence', $request);

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Laminas\Form\Form $form Form
     *
     * @return void
     */
    protected function alterForm($form)
    {
        $evidenceFieldset = $form->get('evidence');
        $evidenceFieldset->get('uploadNowRadio')->setName('uploadNow');
        $evidenceFieldset->get('uploadLaterRadio')->setName('uploadNow');
        $evidenceFieldset->get('sendByPostRadio')->setName('uploadNow');
        $this->getFormHelper()->remove($form, 'evidence->uploadNow');

        $sl = $this->getServiceLocator();
        /** @var \Laminas\Mvc\Controller\Plugin\Url $urlControllerPlugin */
        $urlHelper = $sl->get('Helper\Url');

        /** @var \Common\Service\Helper\TranslationHelperService $translator */
        $translator = $sl->get('Helper\Translation');

        $evidenceHint = $translator->translateReplace(
            'lva-financial-evidence-evidence.hint',
            [
                $urlHelper->fromRoute('guides/guide', ['guide' => 'financial-evidence'], [], true),
            ]
        );
        $evidenceFieldset->setOption('hint', $evidenceHint);
    }
}
