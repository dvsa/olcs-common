<?php

/**
 * Community Licence status formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Community Licence status formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicenceStatus implements FormatterInterface
{
    /**
     * Format
     *
     * @param array                               $data   data
     * @param array                               $column column
     * @param \Laminas\ServiceManager\ServiceManager $sm     service manager
     *
     * @return string
     */
    public static function format($data, $column = [], $sm = null)
    {
        $urlHelper  = $sm->get('Helper\Url');
        $router     = $sm->get('router');
        $request    = $sm->get('request');
        $routeMatch = $router->match($request);
        $matchedRouteName = $routeMatch->getMatchedRouteName();
        $url = $urlHelper->fromRoute(
            $matchedRouteName,
            ['child_id' => $data['id'], 'action' => 'edit'],
            ['query' => $request->getQuery()->toArray()],
            true
        );

        $futureSuspension = $data['futureSuspension'];

        if ($futureSuspension && isset($futureSuspension['startDate']) && isset($futureSuspension['endDate'])) {
            return '<a href="'. $url . '">' .
                'Suspension due: ' .
                self::formatDate($futureSuspension['startDate']) .
                ' to ' .
                self::formatDate($futureSuspension['endDate']) .
                '</a>';
        }

        if ($futureSuspension && isset($futureSuspension['startDate']) && !isset($futureSuspension['endDate'])) {
            return '<a href="'. $url . '">' .
                'Suspension due: ' .
                self::formatDate($futureSuspension['startDate']) .
                '</a>';
        }

        $currentSuspension = $data['currentSuspension'];

        if ($currentSuspension && isset($currentSuspension['startDate']) && isset($currentSuspension['endDate'])) {
            return '<a href="'. $url . '">' .
                'Suspended: ' .
                self::formatDate($currentSuspension['startDate']) .
                ' to ' .
                self::formatDate($currentSuspension['endDate']) .
                '</a>';
        }

        if ($currentSuspension && isset($currentSuspension['startDate']) && !isset($currentSuspension['endDate'])) {
            return '<a href="'. $url . '">' .
                'Suspended: ' .
                self::formatDate($currentSuspension['startDate']) .
                '</a>';
        }

        $currentWithdrawal = $data['currentWithdrawal'];

        if ($currentWithdrawal && isset($currentWithdrawal['startDate'])) {
            return 'Withdrawn: ' . self::formatDate($currentWithdrawal['startDate']);
        }

        if (isset($data['expiredDate'])) {
            return $data['status']['description'] . ': ' . self::formatDate($data['expiredDate']);
        }

        return $data['status']['description'];
    }

    /**
     * Format date
     *
     * @param DateTime $date date
     *
     * @return bool|string
     */
    private static function formatDate($date)
    {
        return date(\DATE_FORMAT, strtotime($date));
    }
}
