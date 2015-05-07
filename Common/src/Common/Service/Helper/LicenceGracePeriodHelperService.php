<?php

/**
 * LicenceGracePeriodHelperService.php
 */
namespace Common\Service\Helper;

/**
 * Class LicenceGracePeriodHelperService
 *
 * Provide a licence context around grace periods and facilitate licence grace period business rules.
 *
 * @package Common\Service\Helper
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceGracePeriodHelperService extends AbstractHelperService
{
    /**
     * Determine whether a grace period is active or not.
     *
     * @param array $gracePeriod
     *
     * @return boolean True if active, false otherwise.
     */
    public function isActive(array $gracePeriod = array())
    {
        if (!isset($gracePeriod['startDate']) || !isset($gracePeriod['endDate'])) {
            throw new \InvalidArgumentException(__METHOD__ . ' expects a valid start and end date.');
        }

        $dateHelper = $this->getServiceLocator()->get('Helper\Date');

        $today = $dateHelper->getDateObject();
        $startDate = $dateHelper->getDateObject($gracePeriod['startDate']);
        $endDate = $dateHelper->getDateObject($gracePeriod['endDate']);

        if ($startDate <= $today && $endDate >= $today) {
            return true;
        }

        return false;
    }
}
