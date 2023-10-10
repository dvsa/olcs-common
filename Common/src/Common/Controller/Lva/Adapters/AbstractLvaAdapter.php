<?php

namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\LvaAdapterInterface;
use Interop\Container\ContainerInterface;
use Laminas\Form\Form;

abstract class AbstractLvaAdapter extends AbstractControllerAwareAdapter implements LvaAdapterInterface
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * Alter the form based on the LVA rules
     *
     * @param \Laminas\Form\Form $form
     */
    public function alterForm(Form $form)
    {
    }
}
