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

    private $tables = ['small', 'medium', 'large'];

    /**
     * Index action
     */
    public function indexAction()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\PsvVehicles');

        foreach ($this->tables as $tableName) {
            $table = $this->getServiceLocator()
                ->get('Table')
                ->prepareTable(
                    'lva-psv-vehicles-' . $tableName,
                    []
                );
            $formHelper->populateFormTable($form->get($tableName), $table);
        }

        $this->getServiceLocator()->get('Script')->loadFile('vehicle-psv');

        return $this->render('vehicles-psv', $form);
    }
}
