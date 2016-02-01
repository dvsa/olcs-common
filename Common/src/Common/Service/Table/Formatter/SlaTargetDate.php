<?php

/**
 * SlaTargetDate formatter
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * SlaTargetDate formatter
 * If set returns link to Sla Target date edit form, if not return link to add form with 'not set' anchor text
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class SlaTargetDate implements FormatterInterface
{
    /**
     * Format an SlaTargetDate
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $urlHelper = $sm->get('Helper\Url');

        if (empty($data['targetDate']))
        {
            $url = $urlHelper->fromRoute(
                'sla-target',
                [
                    'entityType' => 'document',
                    'entityId' => $data['id'],
                    'action' => 'add'
                ]
            );
            return '<a href="' . $url . '" class="js-modal-ajax">Not set</a>';
        } else {
            $url = $urlHelper->fromRoute(
                'sla-target',
                [
                    'entityType' => 'document',
                    'entityId' => $data['id'],
                    'action' => 'edit'
                ]
            );

            $statusHtml = '<span class="status red">Fail</span>';
            if ($data['targetDate'] >= $data['sentDate']) {
                $statusHtml = '<span class="status green">Pass</span>';
            }
            $targetDate = Date::format($data, ['name' => 'targetDate'], $sm);

            return '<a href="' . $url . '" class="js-modal-ajax">' . $targetDate . '</a> ' . $statusHtml;
        }
    }
}
