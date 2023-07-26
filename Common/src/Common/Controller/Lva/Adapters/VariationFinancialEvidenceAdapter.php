<?php

namespace Common\Controller\Lva\Adapters;

use Common\Service\Data\CategoryDataService as Category;
use Interop\Container\ContainerInterface;

class VariationFinancialEvidenceAdapter extends ApplicationFinancialEvidenceAdapter
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }
}
