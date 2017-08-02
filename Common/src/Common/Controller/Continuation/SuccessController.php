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
    /**
     * Index action to handle payment result
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $data = $this->getContinuationDetailData();

        return $this->getViewModel($data['licence']['licNo']);
    }
}
