<?php

/**
 * Abstract Discs Psv Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service\VehicleSafety;

use Zend\Form\Form;
use Common\Controller\Service\AbstractSectionService;

/**
 * Abstract Discs Psv Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractDiscsPsvSectionService extends AbstractSectionService
{
    protected $service = 'psvDisc';

    protected $formTables = array(
        'table' => 'psv_discs'
    );

    public function alterForm(Form $form)
    {
        $this->disableElements($form->get('data'));
        return $form;
    }
}
