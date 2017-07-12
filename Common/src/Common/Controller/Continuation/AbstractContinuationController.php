<?php

namespace Common\Controller\Continuation;

use Common\Controller\Lva\AbstractController;
use Common\Form\Form;
use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\Get as GetContinuationDetail;

/**
 * AbstractContinuationController
 */
abstract class AbstractContinuationController extends AbstractController
{
    protected $layout = 'pages/continuation';

    /**
     * Get the ViewModel used for continuations
     *
     * @param string    $licNo     Licence number eg OB1234567
     * @param Form|null $form      Form to display on the page
     * @param array     $variables additional variables to view
     *
     * @return ViewModel
     */
    protected function getViewModel($licNo, Form $form = null, $variables = [])
    {
        $view = new ViewModel(
            array_merge(['licNo' => $licNo, 'form' => $form], $variables)
        );

        $view->setTemplate($this->layout);

        return $view;
    }

    /**
     * Get a form
     *
     * @param string $formServiceName form service name of the form to generate
     * @param array  $data            data to alter the form
     *
     * @return Form
     */
    protected function getForm($formServiceName, $data = [])
    {
        return $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get($formServiceName)
            ->getForm($data);
    }

    /**
     * Get the continuation detail ID
     *
     * @return int
     */
    protected function getContinuationDetailId()
    {
        return (int)$this->params('continuationDetailId');
    }

    /**
     * Get continuation fee data
     *
     * @return array
     */
    protected function getContinuationDetailData()
    {
        $response = $this->handleQuery(
            GetContinuationDetail::create(
                ['id' => $this->getContinuationDetailId()]
            )
        );
        if (!$response->isOk()) {
            $this->addErrorMessage('unknown-error');
        }
        return $response->getResult();
    }

    /**
     * Redirect to success page
     *
     * @return \Zend\Http\Response
     */
    protected function redirectToSuccessPage()
    {
        return $this->redirect()->toRoute('continuation/success', [], [], true);
    }

    /**
     * Refresh current pages
     *
     * @return \Zend\Http\Response
     */
    protected function redirectToPaymentPage()
    {
        return $this->redirect()->toRoute('continuation/payment', [], [], true);
    }
}
