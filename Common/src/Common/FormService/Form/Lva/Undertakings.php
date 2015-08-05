<?php

/**
 * Undertakings Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Undertakings Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Undertakings extends AbstractFormService
{
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm('Lva\ApplicationUndertakings');

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        return $form;
    }
}
