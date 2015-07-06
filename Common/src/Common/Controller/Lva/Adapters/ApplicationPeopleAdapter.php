<?php

/**
 * Common (aka Internal) Application People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;

/**
 * Common (aka Internal) Application People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ApplicationPeopleAdapter extends VariationPeopleAdapter
{
    public function alterFormForOrganisation(Form $form, $table)
    {
        if (!$this->hasInforceLicences()) {
            return;
        }

        return parent::alterFormForOrganisation($form, $table);
    }

    public function alterAddOrEditFormForOrganisation(Form $form)
    {
        if (!$this->hasInforceLicences()) {
            return;
        }

        return parent::alterAddOrEditFormForOrganisation($form);
    }
}
