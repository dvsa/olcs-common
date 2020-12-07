<?php

/**
 * Document subcategory formatter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Document subcategory formatter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentSubcategory implements FormatterInterface
{
    /**
     * Format a address
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $str = $data['documentSubCategoryName'];
        if ($data['isExternal']) {
            $str .= ' (selfserve)';
        }
        if ($data['ciId']) {
            $str .= ' (emailed)';
        }
        return $str;
    }
}
