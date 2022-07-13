<?php

/**
 * Printer Document Category formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Printer Document Category formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrinterDocumentCategory implements FormatterInterface
{
    /**
     * @param array $data
     * @param array $column
     * @param ServiceManager $sm
     *
     * @return string
     */
    public static function format($row, $column = [], $sm = null)
    {
        $urlHelper = $sm->get('Helper\Url');

        $url = $urlHelper->fromRoute(
            'admin-dashboard/admin-team-management',
            ['rule' => $row['id'], 'action' => 'editRule', 'team' => $row['team']['id']]
        );

        $categories = isset($row['subCategory']) ?
            $row['subCategory']['category']['description'] . ' / ' . $row['subCategory']['subCategoryName'] :
            'Default setting';

        return '<a href="'. $url . '" class="govuk-link js-modal-ajax">' . $categories .'</a>';
    }
}
