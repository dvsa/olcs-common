<?php

namespace Common\Service\Data;

/**
 * Interface AddressProviderInterface
 * @package Common\Service\Data
 */
interface AddressProviderInterface
{
    /**
     * Fetch array of addresses
     *
     * @return array
     */
    public function fetchAddressListData();
}
