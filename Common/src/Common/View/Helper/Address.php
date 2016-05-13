<?php

namespace Common\View\Helper;

use Common\View\Helper\Traits\Utils;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtml;

/**
 * Address view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class Address extends AbstractHelper
{
    use Utils;

    /**
     * Get the HTML to render an address array
     *
     * @param array $address
     *
     * @return string HTML
     */
    public function __invoke(array $address, array $fields = null, $glue = ', ')
    {
        $parts = array();

        if (isset($address['countryCode']['id'])) {
            $address['countryCode'] = $address['countryCode']['id'];
        } else {
            $address['countryCode'] = null;
        }

        if (!isset($fields)) {
            $fields = [
                'addressLine1',
                'addressLine2',
                'addressLine3',
                'town',
                'postcode',
                'countryCode',
            ];
        }

        foreach ($fields as $item) {
            if (isset($address[$item]) && !empty($address[$item])) {
                $parts[] = $this->escapeHtml($address[$item]);
            }
        }

        return implode($glue, $parts);
    }
}
