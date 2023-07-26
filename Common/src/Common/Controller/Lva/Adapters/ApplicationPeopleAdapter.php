<?php

namespace Common\Controller\Lva\Adapters;

use Interop\Container\ContainerInterface;

class ApplicationPeopleAdapter extends VariationPeopleAdapter
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }
}
