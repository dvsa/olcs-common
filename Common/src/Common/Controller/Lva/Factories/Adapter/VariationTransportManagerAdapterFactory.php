<?php

namespace Common\Controller\Lva\Factories\Adapter;

use Common\Controller\Lva\Adapters\VariationTransportManagerAdapter;

/**
 * Factory Class to create instance of Variation TMs
 */
class VariationTransportManagerAdapterFactory extends AbstractTransportManagerAdapterFactory
{
    protected $adapterClass = VariationTransportManagerAdapter::class;
}
