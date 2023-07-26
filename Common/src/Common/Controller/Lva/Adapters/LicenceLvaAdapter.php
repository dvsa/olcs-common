<?php

namespace Common\Controller\Lva\Adapters;

use Interop\Container\ContainerInterface;
use Laminas\Form\Form;

class LicenceLvaAdapter extends AbstractLvaAdapter
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function getIdentifier()
    {
    }

    /**
     * Alter the form based on the LVA rules
     *
     * @param \Laminas\Form\Form $form
     */
    public function alterForm(Form $form)
    {
        $form->get('form-actions')->remove('saveAndContinue');
    }
}
