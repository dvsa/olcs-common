<?php

/**
 * People Adapter Interface
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

use Zend\Form\Form;

/**
 * People Adapter Interface
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
interface PeopleAdapterInterface
{
    public function alterFormForOrganisation(Form $form, $table, $orgId);

    public function alterFormForPartnership(Form $form, $table, $orgId);

    public function alterSoleTraderFormForOrganisation(Form $form, $orgId);

    public function alterAddOrEditFormForOrganisation(Form $form, $orgId);

    public function canModify($orgId);
}
