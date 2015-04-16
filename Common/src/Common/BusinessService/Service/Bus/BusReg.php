<?php

/**
 * Bus Reg
 */
namespace Common\BusinessService\Service\Bus;

use Common\BusinessService\BusinessServiceInterface;
use Common\Service\Entity\FeeEntityService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Bus Reg
 */
class BusReg implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
    }

    /**
     * Returns whether a bus reg may be granted
     *
     * @param $id
     *
     * @return Bool
     */
    public function isGrantable($id)
    {
        $busReg = $this->getServiceLocator()->get('Entity\BusReg')->getDataForGrantable($id);

        // mendatory fields which needs to be marked as Yes
        $yesFields = [
            'timetableAcceptable',
            'mapSupplied',
            'trcConditionChecked',
            'copiedToLaPte',
            'laShortNote',
            'applicationSigned'
        ];

        if (!empty($busReg['busNoticePeriod']) && $busReg['busNoticePeriod']['id'] == 1) {
            // for Scottish registrations opNotifiedLaPte is required
            $yesFields[] = 'opNotifiedLaPte';
        }

        foreach ($yesFields as $field) {
            if (empty($busReg[$field]) || $busReg[$field] != 'Y') {
                return false;
            }
        }

        // mendatory fields which can't be empty
        $nonEmptyFields = [
            'effectiveDate',
            'receivedDate',
            'serviceNo',
            'startPoint',
            'finishPoint',
            'busServiceTypes',
            'trafficAreas',
            'localAuthoritys'
        ];

        foreach ($nonEmptyFields as $field) {
            if (empty($busReg[$field])) {
                return false;
            }
        }

        if (($busReg['isShortNotice'] == 'Y')
            && (empty($busReg['shortNotice']) || false === $this->isGrantableBasedOnShortNotice($busReg['shortNotice']))
        ) {
            // bus reg without short notice details or with one which makes it non-grantable
            return false;
        }

        if ((false === $this->isGrantableBasedOnFee($id))) {
            // bus reg with a fee which makes it non-grantable
            return false;
        }

        return true;
    }

    /**
     * Returns whether a bus reg has short notice details which makes it grantable
     *
     * @param $shortNotice
     *
     * @return Bool
     */
    private function isGrantableBasedOnShortNotice($shortNotice)
    {
        if (empty($shortNotice)) {
            // no short notice makes it non-grantable
            return false;
        }

        $shortNoticQuestionFields = [
            ['change' => 'bankHolidayChange'],
            ['change' => 'connectionChange', 'detail' => 'connectionDetail'],
            ['change' => 'holidayChange', 'detail' => 'holidayDetail'],
            ['change' => 'notAvailableChange', 'detail' => 'notAvailableDetail'],
            ['change' => 'policeChange', 'detail' => 'policeDetail'],
            ['change' => 'replacementChange', 'detail' => 'replacementDetail'],
            ['change' => 'specialOccasionChange', 'detail' => 'specialOccasionDetail'],
            ['change' => 'timetableChange', 'detail' => 'timetableDetail'],
            ['change' => 'trcChange', 'detail' => 'trcDetail'],
            ['change' => 'unforseenChange', 'detail' => 'unforseenDetail'],
        ];

        $hasShortNoticeDetails = false;

        // for short notice at least one question should be Yes
        // and corresponding textarea (if there is one) should not be empty
        foreach ($shortNoticQuestionFields as $questionField) {
            if (!empty($shortNotice[$questionField['change']])
                && $shortNotice[$questionField['change']] == 'Y'
            ) {
                // marked as Yes
                if (!empty($questionField['detail'])) {
                    // detail field exists for the question
                    if (!empty($shortNotice[$questionField['detail']])) {
                        // value of the detail field not empty
                        $hasShortNoticeDetails = true;
                        break;
                    }
                } else {
                    // no detail field for the question
                    $hasShortNoticeDetails = true;
                    break;
                }
            }
        }

        return $hasShortNoticeDetails;
    }

    /**
     * Returns whether a bus reg has a fee which makes it grantable
     *
     * @param $busRegId
     *
     * @return Bool
     */
    private function isGrantableBasedOnFee($busRegId)
    {
        $fee = $this->getServiceLocator()->get('Entity\Fee')->getLatestFeeForBusReg($busRegId);

        if (empty($fee)) {
            // no fee makes it grantable
            return true;
        }

        if (!empty($fee['feeStatus'])
            && in_array($fee['feeStatus']['id'], [FeeEntityService::STATUS_PAID, FeeEntityService::STATUS_WAIVED])
        ) {
            // the fee is paid or waived
            return true;
        }

        return false;
    }
}
