<?php

/**
 * Address view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Address view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class Address extends AbstractHelper
{
    /**
     * Get the HTML to render an address array
     *
     * @param array $address
     *
     * @return string HTML
     */
    public function __invoke(
        array $address,
        $fields = [
            'addressLine1',
            'addressLine2',
            'addressLine3',
            'town',
            'postcode',
            'countryCode'
        ]
    )
    {

        $parts = array();

        if (isset($address['countryCode']['id'])) {
            $address['countryCode'] = $address['countryCode']['id'];
        } else {
            $address['countryCode'] = null;
        }

        foreach ($fields as $item) {

            if (isset($address[$item]) && !empty($address[$item])) {

                $parts[] = $address[$item];
            }
        }

        return implode(', ', $parts);

    }
}
