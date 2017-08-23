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
     * @param \Zend\Http\Request $request Request
     *
     * @return \Zend\Form\Form
     */
    public function getForm(\Zend\Http\Request $request)
    {
        $form = $this->getFormHelper()->createFormWithRequest('Lva\FinancialEvidence', $request);

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form Form
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
        /** @var \Zend\Mvc\Controller\Plugin\Url $urlControllerPlugin */
        $urlControllerPlugin = $sl->get('ControllerPluginManager')->get('url');

        /** @var \Common\Service\Helper\TranslationHelperService $translator */
        $translator = $sl->get('Helper\Translation');

        $evidenceHint = $translator->translateReplace(
            'lva-financial-evidence-evidence.hint',
            [
                $urlControllerPlugin->fromRoute('guides/guide', ['guide' => 'financial-evidence'], [], true),
            ]
        );
        $evidenceFieldset->setOption('hint', $evidenceHint);
    }
}
