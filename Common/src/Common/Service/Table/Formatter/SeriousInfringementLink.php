<?php

/**
 * Class SeriousInfringementLink
 */
namespace Common\Service\Table\Formatter;

/**
 * Class SeriousInfringementLink
 * @package Common\Service\Table\Formatter
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SeriousInfringementLink implements FormatterInterface
{
    /**
     * Return a the serious infringement URL for a table.
     *
     * @param array $data The row data.
     * @param array $column The column
     * @param null $sm The service manager
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        unset($column);

        $urlHelper = $sm->get('Helper\Url');
        $url = $urlHelper->fromRoute('case_penalty_applied', array('si' => $data['id'], 'action' => 'index'), [], true);

        return '<a class="govuk-link" href="' . $url . '">' . $data['id'] . '</a>';
    }
}
