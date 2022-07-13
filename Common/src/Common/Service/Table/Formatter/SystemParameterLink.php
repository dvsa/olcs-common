<?php

/**
 * System parameter link formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * System parameter link formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SystemParameterLink implements FormatterInterface
{
    /**
     * Format
     *
     * @param array $data
     * @param array $column
     * @param ServiceManager $sm
     * @inheritdoc
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute(
            'admin-dashboard/admin-system-parameters',
            ['action' => 'edit', 'sp' => $data['id']]
        );

        return '<a href="' . $url . '" class="govuk-link js-modal-ajax">' . $data['id'] . '</a>';
    }
}
