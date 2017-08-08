<?php

namespace Common\Controller\Continuation;

use Zend\View\Model\ViewModel;
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
    /** @var string */
    protected $layout = 'pages/continuation-success';

    /**
     * Index action to handle payment result
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $data = $this->getContinuationDetailData();
        $licence = $data['licence'];

        $params = [
            'paymentRef' => $data['reference'],
            'isPhysicalSignature' => $data['isPhysicalSignature'],
            'isFinancialEvidenceRequired' => $data['isFinancialEvidenceRequired'],
            'isNi' => $licence['trafficArea']['isNi'],
            'licenceId' => $licence['id'],
        ];

        return $this->getViewModel($licence['licNo'], null, $params);
    }
}
