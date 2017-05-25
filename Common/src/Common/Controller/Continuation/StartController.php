<?php

namespace Common\Controller\Continuation;

use Common\Form\Model\Form\Continuation\Start;
use Zend\View\Model\ViewModel;

/**
 * StartController
 */
class StartController extends AbstractContinuationController
{
    /**
     * Index page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $continuationDetailId = $this->getContinuationDetailId();
        $form = $this->getForm(Start::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->redirect()->toRoute('continuation/checklist', [], [], true);
            }
        }

        return $this->getViewModel('[THIS IS THE LIC NO]', $form);
    }
}
