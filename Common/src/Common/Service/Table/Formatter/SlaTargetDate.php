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

        if (empty($data['slaTargetDate']))
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
                'case_licence_docs_attachments/sla-target',
                [
                    'entityType' => 'document',
                    'entityId' => $data['id'],
                    'action' => 'edit'
                ]
            );
            return '<a href="/' . $url . '" class="js-modal-ajax">' . $data['slaTargetDate'] . '</a>';
        }
    }
}
