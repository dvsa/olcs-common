<?php

namespace Common\Controller\Lva\Factories\Adapter;

use Common\Controller\Lva\Adapters\LicenceTransportManagerAdapter;

/**
 * Factory Class to create instance of Licence TMs
 */
class LicenceTransportManagerAdapterFactory extends AbstractTransportManagerAdapterFactory
{
    protected $adapterClass = LicenceTransportManagerAdapter::class;
}
