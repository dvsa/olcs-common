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

    protected $section = 'vehicles_psv';

    private $tables = ['small', 'medium', 'large'];

    /**
     * Index action
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($this->getFormData());
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper
            ->createForm('Lva\PsvVehicles')
            ->setData($data);

        foreach ($this->tables as $tableName) {
            $table = $this->getServiceLocator()
                ->get('Table')
                ->prepareTable(
                    'lva-psv-vehicles-' . $tableName,
                    // @TODO data
                    []
                );

            $formHelper->populateFormTable(
                $form->get($tableName),
                $table,
                $tableName
            );
        }

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction($data);

            /*
            if ($crudAction !== null) {
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }
             */

            if ($form->isValid()) {

                $this->save($data);

                $this->postSave('vehicles_psv');

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('vehicles_psv');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('vehicle-psv');

        return $this->render('vehicles_psv', $form);
    }

    /**
     * Override the get crud action method
     *
     * @param array $formTables
     * @return array
     */
    protected function etCrudAction(array $formTables = array())
    {
        $data = (array)$this->getRequest()->getPost();

        echo "<pre>"; var_dump($data); die();

        foreach ($this->tables as $section) {

            if (isset($data[$section]['table']['action'])) {

                $action = $this->getActionFromCrudAction($data[$section]['table']);

                $data[$section]['table']['routeAction'] = $section . '-' . strtolower($action);

                return $data[$section]['table'];
            }
        }

        return null;
    }

    protected function getFormData()
    {
        // @TODO not in abstract, references 'Application'
        return $this->getServiceLocator()
            ->get('Entity\Application')
            ->getDataForVehiclesPsv($this->params('id'));
    }

    protected function formatDataForForm($data)
    {
        return array(
            'data' => array(
                'version' => $data['version'],
                'hasEnteredReg' => $data['hasEnteredReg']
            )
        );
    }

    protected function formatDataForSave($data)
    {
        return $data['data'];
    }

    protected function save($data)
    {
        $data = $this->formatDataForSave($data);
        // @TODO remove ref to 'Application'
        $data['id'] = $this->params('id');
        return $this->getServiceLocator()->get('Entity\Application')->save($data);
    }

    /**
     * Override the get crud action method
     *
     * @param array $formTables
     * @return array
     */
    protected function getCrudAction(array $formTables = array())
    {
        $data = $formTables;

        foreach ($this->tables as $section) {

            if (isset($data[$section]['action'])) {

                $action = $this->getActionFromCrudAction($data[$section]);

                $data[$section]['routeAction'] = $section . '-' . strtolower($action);

                return $data[$section];
            }
        }

        return null;
    }

    public function smallAddAction()
    {
        return $this->addOrEdit('add', 'small');
    }

    public function smallEditAction()
    {
        return $this->addOrEdit('edit', 'small');
    }

    protected function addOrEdit($mode, $type)
    {
        $form = $this->getServiceLocator()
            ->get('Helper\Form')
            ->createForm('Lva\PsvVehiclesVehicle');

        return $this->render($mode . '_vehicle', $form);
    }
}
