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
        'postcode' => ''
    );

    /**
     * Format an address from BS7666
     *
     * @param array $address
     * @return array
     */
    public function formatPostalAddressFromBs7666($address)
    {
        $details = $this->details;

        $addressLines = array(
            $this->formatSaon($address),
            trim($this->formatPaon($address) . ' ' . $this->getIndexOr($address, 'street_description')),
            $address['locality_name']
        );

        $lineNo = 1;

        foreach ($addressLines as $line) {

            if (!is_null($line) && $line !== '') {
                $details['addressLine' . $lineNo] = $this->formatString($line);
                $lineNo++;
            }
        }
        $details['town'] = $this->formatString($address['post_town']);

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

            $address = $this->formatPostalAddressFromBS7666($item);

            $allowedParts = array('addressLine1', 'addressLine2', 'addressLine3', 'town');
            $parts = array();

            foreach ($address as $key => $val) {
                if (in_array($key, $allowedParts) && !empty($val)) {
                    $parts[] = $val;
                }
            }

            $str = implode(', ', $parts);

            if (strlen($str) >= self::MAX_DISPLAY_LENGTH) {
                // one char is correct; we use an ellipsis rather than three dots
                $str = substr($str, 0, self::MAX_DISPLAY_LENGTH - 1) . '…';
            }

            $options[$item['uprn']] = $str;
        }

        return $options;
    }

    /**
     * Format a string for the lines
     *
     * @param string $string
     * @return string
     */
    private function formatString($string)
    {
        return ucwords(strtolower($string));
    }

    /**
     * Format the SAON
     *
     * @return string
     */
    private function formatSaon($address)
    {
        return $this->formatOn($address, 'sao', 'organisation_name');
    }

    /**
     * Format the PAON
     *
     * @return string
     */
    private function formatPaon($address)
    {
        return $this->formatOn($address, 'pao', 'building_name');
    }

    /**
     * Shared logic to format the ^ons
     *
     * @param string $prefix
     * @param string $simple
     * @return string
     */
    private function formatOn($address, $prefix, $simple)
    {
        $string = '';

        if ($this->getIndexOr($address, $simple) !== '') {
            $string .= $this->getIndexOr($address, $simple);
        } else {

            $endNumber = $this->getIndexOr($address, $prefix . '_end_number') !== ''
                ? ('-' . $this->getIndexOr($address, $prefix . '_end_number'))
                : '';

            $string .= sprintf(
                '%s%s%s%s',
                $this->getIndexOr($address, $prefix . '_start_number'),
                $this->getIndexOr($address, $prefix . '_start_prefix'),
                $endNumber,
                $this->getIndexOr($address, $prefix . '_end_suffix')
            );

            if ($this->getIndexOr($address, $prefix . '_start_number') !== ''
                && $this->getIndexOr($address, $prefix . '_text') !== ''
            ) {
                $string .= ' ' . $this->getIndexOr($address, $prefix . '_text');
            }
        }

        return trim($string);
    }

    /**
     * Get index or the default
     *
     * @param array $array
     * @param string $index
     * @param mixed $default
     */
    private function getIndexOr($array, $index, $default = '')
    {
        return (isset($array[$index]) ? $array[$index] : $default);
    }
}
