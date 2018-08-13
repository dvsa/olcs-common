<?php

namespace Common\View\Helper;

use Common\View\Helper\Traits\Utils;
use Zend\View\Helper\AbstractHelper;

/**
 * Format Currency in the system appropriately
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class CurrencyFormatter extends AbstractHelper
{
    use Utils;

    /**
     * Return a formatted Monetary Value
     *
     * @param string|null $value Parameters
     *
     * @return string
     */
    public function __invoke(?string $value): string
    {
        if (substr($value, strlen($value) - 3) === '.00') {
            return sprintf("£" . $this->escapeHtml(substr($value, 0, strlen($value) - 3)));
        }

        return sprintf("£" . $this->escapeHtml($value));
    }
}
