<?php

namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractPeopleAdapter;
use Interop\Container\ContainerInterface;

class LicencePeopleAdapter extends AbstractPeopleAdapter
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }
}
