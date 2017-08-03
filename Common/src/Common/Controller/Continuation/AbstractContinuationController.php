<?php

namespace Common\Controller\Continuation;

use Common\Controller\Lva\AbstractController;
use Common\Form\Form;
use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\Get as GetContinuationDetail;
use Common\RefData;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Exception;

/**
 * AbstractContinuationController
 */
abstract class AbstractContinuationController extends AbstractController
{
    const SUCCESS_CONTROLLER = 'ContinuationController/Success';
    const LICENCE_OVERVIEW_ROUTE = 'lva-licence';

    /** @var string  */
    protected $layout = 'pages/continuation';

    /** @var array */
    protected $continuationData;

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
     * @param bool $forceReload Force reload of data
     *
     * @return array
     */
    protected function getContinuationDetailData($forceReload = false)
    {
        if ($forceReload || $this->continuationData === null) {
            $response = $this->handleQuery(
                GetContinuationDetail::create(
                    ['id' => $this->getContinuationDetailId()]
                )
            );
            $this->continuationData = $response->getResult();
            if (!$response->isOk()) {
                $this->addErrorMessage('unknown-error');
            }
        }
        return $this->continuationData;
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
     * Redirect to payment page
     *
     * @return \Zend\Http\Response
     */
    protected function redirectToPaymentPage()
    {
        return $this->redirect()->toRoute('continuation/payment', [], [], true);
    }

    /**
     * Redirect to licence overview page
     *
     * @return \Zend\Http\Response
     */
    protected function redirectToLicenceOverviewPage($licenceId)
    {
        return $this->redirect()->toRoute(self::LICENCE_OVERVIEW_ROUTE, ['licence' => $licenceId], [], true);
    }

    /**
     * Execute the request
     *
     * @param MvcEvent $e Event
     *
     * @return null|\Zend\Http\Response
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            throw new Exception\DomainException('Missing route matches; unsure how to retrieve action');
        }
        $data = $this->getContinuationDetailData();
        $status = isset($data['status']['id']) ? $data['status']['id'] : null;
        $controller = $routeMatch->getParam('controller');

        if ($controller !== self::SUCCESS_CONTROLLER) {
            if ($status === RefData::CONTINUATION_STATUS_COMPLETE && (int) $data['isDigital'] === 1) {
                return $this->redirectToSuccessPage();
            }
            if ($status === RefData::CONTINUATION_STATUS_GENERATED) {
                return parent::onDispatch($e);
            }
        } else {
            if ($status === RefData::CONTINUATION_STATUS_COMPLETE) {
                return parent::onDispatch($e);
            }
        }

        return $this->redirectToLicenceOverviewPage($data['licence']['id']);
    }
}
