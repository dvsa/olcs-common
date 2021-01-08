<?php

namespace Common\Controller\Continuation;

use Laminas\View\Model\ViewModel;

/**
 * StartController
 */
class StartController extends AbstractContinuationController
{
    const BACK_ROUTE = 'lva-licence';

    /** @var string  */
    protected $layout = 'pages/continuation-start';

    protected $currentStep = self::STEP_START;

    /**
     * Index page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $data = $this->getContinuationDetailData(
            $this->getContinuationDetailId()
        );
        $licenceData = $data['licence'];

        $form = $this->getForm('continuations-start');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->redirect()->toRoute('continuation/checklist', [], [], true);
            }
        }

        return $this->getViewModel(
            $licenceData['licNo'],
            $form,
            ['backRoute' => self::BACK_ROUTE, 'backRouteParams' => ['licence' => $licenceData['id']]]
        );
    }
}
