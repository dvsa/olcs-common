<?php

namespace Common\Controller\Lva\Factories\Adapter;

use Common\Controller\Lva\Adapters\ApplicationTransportManagerAdapter;

/**
 * Factory Class to create instance of Application TMs
 */
class ApplicationTransportManagerAdapterFactory extends AbstractTransportManagerAdapterFactory
{
    protected $adapterClass = ApplicationTransportManagerAdapter::class;
}
