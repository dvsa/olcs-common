<?php

namespace Common\Controller\Continuation;

use Common\Form\Declaration;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\Submit;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

/**
 * DeclarationController
 */
class DeclarationController extends AbstractContinuationController
{
    /**
     * Index page
     *
     * @return ViewModel|Response
     */
    public function indexAction()
    {
        $continuationDetail = $this->getContinuationDetailData();

        /** @var \Common\Form\Form $form */
        $form = $this->getForm(\Common\FormService\Form\Continuation\Declaration::class, $continuationDetail);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                // If using Verify to sign
                if ($this->isButtonPressed('sign')) {
                    return $this->redirect()->toRoute(
                        'verify/initiate-request',
                        [
                            'continuationDetailId' => $continuationDetail['id'],
                        ]
                    );
                } else {
                    // Using Print to sign
                    // Submit the continuation
                    $this->handleCommand(
                        Submit::create(['id' => $continuationDetail['id'], 'version' => $form->getData()['version']])
                    );
                    // Goto to page depenedant on whether fees need to be paid
                    if ($continuationDetail['hasOutstandingContinuationFee']) {
                        return $this->redirectToPaymentPage();
                    }
                    return $this->redirectToSuccessPage();
                }
            }
        }

        $vars = [
            'backRoute' => 'continuation/finances',
        ];
        return $this->getViewModel($continuationDetail['licence']['licNo'], $form, $vars);
    }

    /**
     * Get form
     *
     * @return Form
     */
    protected function getDeclarationForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm(
            \Common\Form\Model\Form\Continuation\Declaration::class
        );
    }
}
