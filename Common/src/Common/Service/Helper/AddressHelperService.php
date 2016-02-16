<?php

/**
 * Address helper handles address formatting
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

/**
 * Address helper handles address formatting
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddressHelperService extends AbstractHelperService
{
    /**
     * Max length we'll display in the address dropdown before truncating
     */
    const MAX_DISPLAY_LENGTH = 50;

    /**
     * Holds the template for the details
     */
    private $details = array(
        'addressLine1' => '',
        'addressLine2' => '',
        'addressLine3' => '',
        'addressLine4' => '',
        'town' => '',
        'postcode' => '',
        'countryCode' => 'GB',
    );

    /**
     * Format an address
     *
     * @param array $address
     * @return array
     */
    public function formatPostalAddress($address)
    {
        $details = $this->details;

        $details['addressLine1'] = $address['address_line1'];
        $details['addressLine2'] = $address['address_line2'];
        $details['addressLine3'] = $address['address_line3'];
        $details['addressLine4'] = $address['address_line4'];
        $details['town'] = $address['post_town'];
        $details['postcode'] = $address['postcode'];

        return $details;
    }

    /**
     * Format a list of addresses for a dropdown list
     *
     * @param array $list
     * @return array
     */
    public function formatAddressesForSelect($list)
    {
        $options  = array();
        foreach ($list as $item) {

            $address = $this->formatPostalAddress($item);

            $allowedParts = array('addressLine1', 'addressLine2', 'addressLine3', 'town');
            $parts = array();

            foreach ($address as $key => $val) {
                if (in_array($key, $allowedParts) && !empty($val)) {
                    $parts[] = $val;
                }
            }

            $str = implode(', ', $parts);

            if (strlen($str) > self::MAX_DISPLAY_LENGTH) {
                // one char is correct; we use an ellipsis rather than three dots
                $str = substr($str, 0, self::MAX_DISPLAY_LENGTH - 1) . '…';
            }

            $options[$item['uprn']] = $str;
            asort($options, SORT_NATURAL);
        }

        return $options;
    }
}
