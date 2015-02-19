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
    public function addMessages($orgId);

    public function alterFormForOrganisation(Form $form, $table, $orgId);

    public function alterAddOrEditFormForOrganisation(Form $form, $orgId, $orgType);

    public function canModify($orgId);
}
