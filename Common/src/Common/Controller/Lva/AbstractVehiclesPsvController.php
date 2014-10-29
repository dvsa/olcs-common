<?php

/**
 * Vehicles PSV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Zend\Form\Form;

/**
 * Vehicles PSV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractVehiclesPsvController extends AbstractController
{
    use Traits\CrudTableTrait;

    protected $section = 'vehicles-psv';

    /**
     * Index action
     */
    public function indexAction()
    {
        $form = $this->getServiceLocator()
            ->get('Helper\Form')
            ->createForm('Lva\PvsVehicles');

        //$this->getServiceLocator()->get('Script')->loadFile('???');

        return $this->render('vehicles-psv', $form);
    }
}
