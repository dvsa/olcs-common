<?php

/**
 * Abstract Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractControllerAwareAdapter;
use Common\Controller\Lva\Interfaces\TransportManagerAdapterInterface;

/**
 * Abstract Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
abstract class AbstractTransportManagerAdapter extends AbstractControllerAwareAdapter implements
    TransportManagerAdapterInterface
{
    /**
     * Get transport managers form
     *
     * @return \Zend\Form\Form
     */
    public function getForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\TransportManagers');
    }

    /**
     * Get the table
     *
     * @return \Common\Service\Table\TableBuilder
     */
    public function getTable($template = 'lva-transport-manangers')
    {
        return $this->getServiceLocator()->get('Table')->prepareTable($template);
    }

    /**
     * Is this licence required to have at least one Transport Manager
     *
     * @return boolean
     */
    public function mustHaveAtLeastOneTm()
    {
        return false;
    }

    /**
     * Add any messages to the page
     */
    public function addMessages($licenceId)
    {
    }

    /**
     * Map array data from the Backend into arrays for CRUD tables
     *
     * @param array $applicationTms array of Transport Manager Applications
     * @param array $licenceTms     array of Transport Manager Licences
     *
     * @return array
     */
    protected function mapResultForTable(array $applicationTms, array $licenceTms = [])
    {
        $mappedData = [];

        // add each TM from the licence
        foreach ($licenceTms as $tml) {
            $mappedData[$tml['transportManager']['id']] = [
                // Transport Manager Licence ID
                'id' => 'L'. $tml['id'],
                'name' => $tml['transportManager']['homeCd']['person'],
                'status' => null,
                'email' => $tml['transportManager']['homeCd']['emailAddress'],
                'dob' => $tml['transportManager']['homeCd']['person']['birthDate'],
                'transportManager' => $tml['transportManager'],
                'action' => 'E',
            ];
        }

        // add each TM from the application/variation
        foreach ($applicationTms as $tma) {
            $mappedData[$tma['transportManager']['id'].'a'] = [
                'id' => $tma['id'],
                'name' => $tma['transportManager']['homeCd']['person'],
                'status' => $tma['tmApplicationStatus'],
                'email' => $tma['transportManager']['homeCd']['emailAddress'],
                'dob' => $tma['transportManager']['homeCd']['person']['birthDate'],
                'transportManager' => $tma['transportManager'],
                'action' => $tma['action'],
            ];
            // update the licence TM's if they have been updated
            switch ($tma['action']) {
                case 'U':
                    // Mark original as the current
                    $mappedData[$tma['transportManager']['id']]['action'] = 'C';
                    break;
                case 'D':
                    // Remove the original so that just the Delete version appears
                    unset($mappedData[$tma['transportManager']['id']]);
                    break;
            }
        }

        // sort the data by TM ID (created order)
        ksort($mappedData);

        return $mappedData;
    }
}
