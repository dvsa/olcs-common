<?php

namespace Common\Controller\Continuation;

use Zend\View\Model\ViewModel;
use Common\Controller\Traits\StoredCardsTrait;
use Dvsa\Olcs\Transfer\Command\Transaction\PayOutstandingFees;
use Dvsa\Olcs\Transfer\Query\Transaction\Transaction as PaymentById;
use Common\RefData;

/**
 * PaymentController
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PaymentController extends AbstractContinuationController
{
    protected $layout = 'pages/fees/pay-one';

    use StoredCardsTrait;

    /**
     * Index page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $data = $this->getContinuationDetailData();
        $fees = $data['fees'];

        $request = $this->getRequest();

        if ($request->isPost()) {
            $storedCards = $request->getPost('storedCards');
            $storedCardReference = (is_array($storedCards) && $storedCards['card'] !== '0')
                ? $storedCards['card']
                : false;

            return $this->payFees(
                array_column($fees, 'id'), $data['licence']['organisation']['id'], $storedCardReference
            );
        }

        if (empty($fees)) {
            $this->addSuccessMessage('payment.error.feepaid');
            return $this->redirectToSuccessPage();
        }

        /* @var $form \Common\Form\Form */
        $form = $this->getForm('continuations-payment', $data);
        $fee = reset($fees);
        $this->setupSelectStoredCards($form, $fee['feeType']['isNi']);

        $viewVariables = [
            'form' => $form,
            'payingFromFlow' => true,
            'hasContinuation' => true,
            'type' => 'fees'
        ];
        if (count($fees) > 1) {
            $table = $this->getServiceLocator()->get('Table')->buildTable('pay-fees', $fees, [], false);
            $viewVariables['table'] = $table;
            $this->layout = 'pages/fees/pay-multi';
        } else {
            $viewVariables['fee'] = $fee;
        }
        return $this->getViewModel($data['licence']['licNo'], $form, $viewVariables);
    }

    /**
     * Calls command to initiate payment and then redirects
     *
     * @param array        $feeIds              fee id
     * @param int          $organisationId      organisation id
     * @param string|false $storedCardReference a reference to the stored card to use
     *
     * @return ViewModel
     */
    protected function payFees($feeIds, $organisationId, $storedCardReference = false)
    {
        $cpmsRedirectUrl = $this->getServiceLocator()
            ->get('Helper\Url')
            ->fromRoute('continuation/success', [], ['force_canonical' => true], true);

        $paymentMethod = RefData::FEE_PAYMENT_METHOD_CARD_ONLINE;
        $dtoData = compact('cpmsRedirectUrl', 'feeIds', 'paymentMethod', 'organisationId', 'storedCardReference');

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->handleCommand(PayOutstandingFees::create($dtoData));

        $result = $this->handleResponse($response);
        if ($result !== null) {
            return $result;
        }

        $paymentId = $response->getResult()['id']['transaction'];

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->handleQuery(PaymentById::create(['id' => $paymentId]));
        $payment = $response->getResult();
        $viewVariables = [
            'gateway' => $payment['gatewayUrl'],
            'data' => ['receipt_reference' => $payment['reference']]
        ];
        $this->layout = 'cpms/payment';

        return $this->getViewModel('', null, $viewVariables);
    }

    /**
     * Handle response
     *
     * @param \Common\Service\Cqrs\Response $response response
     *
     * @return null|\Zend\Http\Response
     */
    protected function handleResponse($response)
    {
        $errorMessage = '';

        $messages = $response->getResult()['messages'];

        $translateHelper = $this->getServiceLocator()->get('Helper\Translation');
        foreach ($messages as $message) {
            if (is_array($message) && array_key_exists(RefData::ERR_WAIT, $message)) {
                $errorMessage = $translateHelper->translate('payment.error.15sec');
                break;
            } elseif (is_array($message) && array_key_exists(RefData::ERR_NO_FEES, $message)) {
                $errorMessage = $translateHelper->translate('payment.error.feepaid');
                break;
            }
        }
        if ($errorMessage !== '') {
            $this->addErrorMessage($errorMessage);
            return $this->redirectToPaymentPage();
        }
        if (!$response->isOk()) {
            $this->addErrorMessage('payment-failed');
            return $this->redirectToPaymentPage();
        }

        return null;
    }
}
