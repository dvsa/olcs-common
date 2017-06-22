<?php

namespace Common\Controller\Continuation;

use Zend\View\Model\ViewModel;

/**
 * DeclarationController
 */
class DeclarationController extends AbstractContinuationController
{
    /**
     * Index page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $continuationDetailId = $this->getContinuationDetailId();
        // @todo Create new form
        $form = $this->getForm('continuations-start');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->redirect()->toRoute('continuation/payment', [], [], true);
            }
        }

        return $this->getViewModel('[THIS IS THE LIC NO]', $form);
    }
}
