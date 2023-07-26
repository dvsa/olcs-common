<?php

namespace Common\Controller\Lva\Adapters;

use Interop\Container\ContainerInterface;

class ApplicationConditionsUndertakingsAdapter extends AbstractConditionsUndertakingsAdapter
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }
}
