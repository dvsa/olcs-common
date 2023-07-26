<?php

namespace Common\Controller\Lva\Adapters;

use Interop\Container\ContainerInterface;
use Laminas\Form\Form;
use Common\Controller\Lva\Interfaces\BusinessTypeAdapterInterface;

class GenericBusinessTypeAdapter extends AbstractAdapter implements BusinessTypeAdapterInterface
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function alterFormForOrganisation(Form $form, $orgId)
    {
        // no-op
    }
}
