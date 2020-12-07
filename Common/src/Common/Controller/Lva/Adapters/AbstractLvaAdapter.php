<?php

/**
 * Abstact Lva Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Laminas\Form\Form;
use Common\Controller\Lva\Interfaces\LvaAdapterInterface;

/**
 * Abstact Lva Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLvaAdapter extends AbstractControllerAwareAdapter implements LvaAdapterInterface
{
    /**
     * Alter the form based on the LVA rules
     *
     * @param \Laminas\Form\Form $form
     */
    public function alterForm(Form $form)
    {
    }
}
