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
            'isSpecialRestricted' => $licence['licenceType']['id'] === RefData::LICENCE_TYPE_SPECIAL_RESTRICTED,
        ];

        // if licence is PSV R, PSV SN or PSV SI
        if ($licence['goodsOrPsv']['id'] === RefData::LICENCE_CATEGORY_PSV &&
            ($licence['licenceType']['id'] === RefData::LICENCE_TYPE_RESTRICTED ||
            $licence['licenceType']['id'] === RefData::LICENCE_TYPE_STANDARD_NATIONAL ||
            $licence['licenceType']['id'] === RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL)
        ) {
            $params['numPsvDiscs'] = $data['totPsvDiscs'];
            $params['licenceDocumentationMessage'] = $params['numPsvDiscs'] > 0 ? 'continuation.success.licence-documentation' : 'continuation.success.licence-documentation.zero.discs';
        }

        return $this->getViewModel($licence['licNo'], null, $params);
    }
}
