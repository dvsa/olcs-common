<?php

/**
 * Address service handles address formatting
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Address;

/**
 * Address service handles address formatting
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Address
{
    /**
     * Holds the address parts
     */
    private $address = array();

    /**
     * Holds the template for the details
     */
    private $details = array(
        'addressLine1' => '',
        'addressLine2' => '',
        'addressLine3' => '',
        'addressLine4' => '',
        'city' => '',
        'postcode' => ''
    );

    /**
     * Set the address
     *
     * @param array $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Get the address
     *
     * @param array $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Get an address part
     *
     * @param string $name
     * @return string
     */
    public function getAddressPart($name)
    {
        return (isset($this->address[$name]) ? (string)$this->address[$name] : '');
    }

    /**
     * Format an address from BS7666
     *
     * @param array $address
     * @return array
     */
    public function formatPostalAddressFromBS7666($address = null)
    {
        if (is_null($address)) {
            $address = $this->getAddress();
        } else {
            $this->setAddress($address);
        }

        $details = $this->details;

        $addressLines = array(
            $this->formatSaon($address),
            trim($this->formatPaon($address) . ' ' . $this->getAddressPart('street_description')),
            $address['locality_name'],
            ($address['town_name'] !== $address['administritive_area'] ? $address['town_name'] : '')
        );

        $lineNo = 1;

        foreach ($addressLines as $line) {

            if (!is_null($line) && $line !== '') {
                $details['addressLine' . $lineNo] = $this->formatString($line);
                $lineNo++;
            }
        }

        if ($address['town_name'] !== $address['administritive_area']) {
            $details['city'] = $this->formatString($address['administritive_area']);
        } else {
            $details['city'] = $this->formatString($address['town_name']);
        }

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

            $allowedParts = array('addressLine1', 'addressLine2', 'addressLine3', 'city');
            $parts = array();

            foreach ($address as $key => $val) {
                if (in_array($key, $allowedParts) && !empty($val)) {
                    $parts[] = $val;
                }
            }

            $options[$item['uprn']] = implode(', ', $parts);
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
     * @param array $address
     * @return string
     */
    private function formatSaon($address)
    {
        return $this->formatOn($address, 'sao', 'organisation_name');
    }

    /**
     * Format the PAON
     *
     * @param array $address
     * @return string
     */
    private function formatPaon($address)
    {
        return $this->formatOn($address, 'pao', 'building_name');
    }

    /**
     * Shared logic to format the ^ons
     *
     * @param array $address
     * @param string $prefix
     * @param string $simple
     * @return string
     */
    private function formatOn($address, $prefix, $simple)
    {
        $string = '';

        if ($this->getAddressPart($simple) !== '') {
            $string .= $this->getAddressPart($simple);
        } else {
            $string .= $this->getAddressPart($prefix . '_start_number')
                . $this->getAddressPart($prefix . '_start_prefix')
                . ($this->getAddressPart($prefix . '_end_number') !== '' ? ('-' . $this->getAddressPart($prefix . '_end_number')) : '')
                . $this->getAddressPart($prefix . '_end_suffix');

            if ($this->getAddressPart($prefix . '_start_number') !== '' && $this->getAddressPart($prefix . '_text') !== '') {
                $string .= ' ' . $this->getAddressPart($prefix . '_text');
            }
        }

        return trim($string);
    }
}
