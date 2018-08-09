<?php

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Format Currency in the system appropriately
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class CurrencyFormatter extends AbstractHelper
{

    /**
     * Return a formatted Monetary Value
     *
     * @param string|null $value Parameters
     *
     * @return string
     */
    public function __invoke(?string $value): string
    {
        $validValue = $this->view->escapeHtml($value);

        if (substr($validValue, strlen($validValue) - 3) === '.00') {
            return sprintf("£" . substr($validValue, 0, strlen($validValue) - 3));
        }

        return sprintf("£" . $validValue);
    }
}
