<?php

namespace Common\Controller\Continuation;

use Zend\View\Model\ViewModel;
use Common\FormService\Form\Continuation\ConditionsUndertakings as ConditionsUndertakingsFormService;

/**
 * Conditions & undertakings controller controller
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 *
 */
class ConditionsUndertakingsController extends AbstractContinuationController
{
    const NEXT_STEP = 'continuation/finances';

    protected $layout = 'pages/continuation-conditions-undertakings';

    protected $currentStep = self::STEP_CU;

    /**
     * Index action
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');

        $data = $this->getContinuationDetailData();
        $data['conditionsUndertakings']['licence']['comment'] = $translator->translate('markup-continuation-psv-restricted-comment');
        $data['conditionsUndertakings']['licence']['required_undertakings'] = $translator->translate('markup-continuation-psv-restricted-required-undertaking');

        $form = $this->getForm(ConditionsUndertakingsFormService::class, $data);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData((array)$request->getPost());
            if ($form->isValid()) {
                $this->redirect()->toRoute(self::NEXT_STEP, [], [], true);
            }
        }

        $params = [
            'backRoute' => 'continuation/checklist',
            'conditionsUndertakings' => $data['conditionsUndertakings'],
        ];
        $this->placeholder()->setPlaceholder('pageTitle', 'continuation.conditions-undertakings.page-title');
        return $this->getViewModel($data['licence']['licNo'], $form, $params);
    }
}
