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
        'town' => '',
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
     * @return array
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
    public function formatPostalAddressFromBs7666($address = null)
    {
        if (is_null($address)) {
            $address = $this->getAddress();
        } else {
            $this->setAddress($address);
        }

        $details = $this->details;

        $addressLines = array(
            $this->formatSaon(),
            trim($this->formatPaon() . ' ' . $this->getAddressPart('street_description')),
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
            $details['town'] = $this->formatString($address['administritive_area']);
        } else {
            $details['town'] = $this->formatString($address['town_name']);
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

            $allowedParts = array('addressLine1', 'addressLine2', 'addressLine3', 'town');
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
     * @return string
     */
    private function formatSaon()
    {
        return $this->formatOn('sao', 'organisation_name');
    }

    /**
     * Format the PAON
     *
     * @return string
     */
    private function formatPaon()
    {
        return $this->formatOn('pao', 'building_name');
    }

    /**
     * Shared logic to format the ^ons
     *
     * @param string $prefix
     * @param string $simple
     * @return string
     */
    private function formatOn($prefix, $simple)
    {
        $string = '';

        if ($this->getAddressPart($simple) !== '') {
            $string .= $this->getAddressPart($simple);
        } else {
            $string .= $this->getAddressPart($prefix . '_start_number')
                . $this->getAddressPart($prefix . '_start_prefix')
                . (
                    $this->getAddressPart($prefix . '_end_number') !== ''
                    ? ('-' . $this->getAddressPart($prefix . '_end_number'))
                    : ''
                )
                . $this->getAddressPart($prefix . '_end_suffix');

            if ($this->getAddressPart($prefix . '_start_number') !== ''
                && $this->getAddressPart($prefix . '_text') !== '') {
                $string .= ' ' . $this->getAddressPart($prefix . '_text');
            }
        }

        return trim($string);
    }
}
