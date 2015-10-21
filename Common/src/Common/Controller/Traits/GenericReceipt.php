<?php

/**
 * Generic receipt functionality
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Traits;

use Common\View\Model\ReceiptViewModel;
use Dvsa\Olcs\Transfer\Query\Transaction\TransactionByReference as PaymentByReference;
use Common\Exception\ResourceNotFoundException;

/**
 * Generic receipt functionality
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
trait GenericReceipt
{
    public function printAction()
    {
        $paymentRef = $this->params()->fromRoute('reference');

        $viewData = $this->getReceiptData($paymentRef);

        $view = new ReceiptViewModel($viewData);

        return $view;
    }

    protected function getReceiptData($paymentRef)
    {
        $query = PaymentByReference::create(['reference' => $paymentRef]);
        $response = $this->handleQuery($query);
        if ($response->isOk()) {
            $payment = $response->getResult();
            $fees = array_map(
                function ($fp) {
                    return $fp['fee'];
                },
                $payment['feeTransactions']
            );
        } else {
            throw new ResourceNotFoundException('Payment not found');
        }

        $table = $this->getServiceLocator()->get('Table')
            ->buildTable('pay-fees', $fees, [], false);

        // override table title
        $tableTitle = $this->getServiceLocator()->get('Helper\Translation')
            ->translate('pay-fees.success.table.title');
        $table->setVariable('title', $tableTitle);

        // get operator name from the first fee
        $operatorName = $fees[0]['licence']['organisation']['name'];

        return compact('payment', 'fees', 'operatorName', 'table');
    }
}
