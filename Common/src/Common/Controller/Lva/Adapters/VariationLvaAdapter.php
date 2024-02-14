<?php

namespace Common\Controller\Lva\Adapters;

use Psr\Container\ContainerInterface;

class VariationLvaAdapter extends AbstractLvaAdapter
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function getIdentifier()
    {
        return $this->getApplicationAdapter()->getIdentifier();
    }
}
