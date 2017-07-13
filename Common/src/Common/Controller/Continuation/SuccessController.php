<?php

namespace Common\Controller\Continuation;

use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Transfer\Command\Transaction\CompleteTransaction as CompletePayment;
use Dvsa\Olcs\Transfer\Query\Transaction\Transaction as PaymentById;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\Get as GetContinuationDetail;
use Common\RefData;

/**
 * Success controller
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 *
 */
class SuccessController extends AbstractContinuationController
{
    /**
     * Index action to handle payment result
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $response = $this->handlePaymentResult();
        if ($response !== null) {
            return $response;
        }

        $data = $this->getContinuationDetailData();

        return $this->getViewModel($data['licence']['licNo']);
    }

    /**
     * Handle payment result
     *
     * @return null|\Zend\Http\Response
     */
    protected function handlePaymentResult()
    {
        $queryStringData = (array) $this->getRequest()->getQuery();
        if (empty($queryStringData)) {
            return null;
        }

        $dtoData = [
            'reference' => $queryStringData['receipt_reference'],
            'cpmsData' => $queryStringData,
            'paymentMethod' => RefData::FEE_PAYMENT_METHOD_CARD_ONLINE,
        ];

        $response = $this->handleCommand(CompletePayment::create($dtoData));

        if (!$response->isOk()) {
            $this->addErrorMessage('payment-failed');
            return $this->redirectToPaymentPage();
        }

        $paymentId = $response->getResult()['id']['transaction'];
        $response = $this->handleQuery(PaymentById::create(['id' => $paymentId]));
        $payment = $response->getResult();

        switch ($payment['status']['id']) {
            case RefData::TRANSACTION_STATUS_COMPLETE:
                $this->addSuccessMessage('payment-completed');
                break;
            case RefData::TRANSACTION_STATUS_CANCELLED:
                $this->addErrorMessage('payment-cancelled');
                return $this->redirectToPaymentPage();
            case RefData::TRANSACTION_STATUS_FAILED:
            default:
                $this->addErrorMessage('payment-failed');
                return $this->redirectToPaymentPage();
        }

        return null;
    }
}
