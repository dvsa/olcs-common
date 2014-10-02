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
    protected $service = 'Licence';
    protected $actionService = 'Licence';

    protected $formTableDataBundle = array(
        'properties' => array(),
        'children' => array(
            'psvDiscs' => array(
                'properties' => array(
                    'id',
                    'discNo',
                    'issuedDate',
                    'ceasedDate',
                    'isCopy'
                )
            )
        )
    );

    protected $actionDataBundle = array(
        'properties' => array(
            'id',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        ),
        'children' => array(
            'psvDiscs' => array(
                'properties' => array(
                    'id',
                    'ceasedDate'
                )
            )
        )
    );

    protected $formTables = array(
        'table' => 'psv_discs'
    );

    protected $formTableData;

    /**
     * Return the form table data
     *
     * @param type $id
     * @param type $table
     * @return type
     */
    public function getFormTableData($id, $table = '')
    {
        if ($this->formTableData === null) {
            $data = $this->getHelperService('RestHelper')
                ->makeRestCall($this->getService(), 'GET', $id, $this->formTableDataBundle);

            $this->formTableData = array();

            foreach ($data['psvDiscs'] as $disc) {
                // Ignore ceased discs
                if (!empty($disc['ceasedDate'])) {
                    continue;
                }
                $disc['discNo'] = $this->getDiscNumberFromDisc($disc);
                $this->formTableData[] = $disc;
            }
        }

        return $this->formTableData;
    }

    /**
     * Override load method so it doesn't do anything
     *
     * @param int $id
     * @return array
     */
    public function load($id)
    {
        return array();
    }

    /**
     * Process load
     *
     * @param array $data
     * @return array
     */
    public function processLoad($data)
    {
        $discs = $this->getFormTableData($this->getIdentifier());

        $data = array(
            'data' => array(
                'validDiscs' => 0,
                'pendingDiscs' => 0
            )
        );

        foreach ($discs as $disc) {
            if (!empty($disc['issuedDate'])) {
                $data['data']['validDiscs']++;
            } else {
                $data['data']['pendingDiscs']++;
            }
        }

        return $data;
    }

    /**
     * Process action load
     *
     * @param array $data
     * @return array
     */
    public function processActionLoad($data)
    {
        $data = $this->getHelperService('RestHelper')
            ->makeRestCall($this->getActionService(), 'GET', $this->getIdentifier(), $this->getActionDataBundle());

        $data['totalAuth'] = (
            $data['totAuthSmallVehicles'] + $data['totAuthMediumVehicles'] + $data['totAuthLargeVehicles']
        );

        $data['discCount'] = 0;

        foreach ($data['psvDiscs'] as $disc) {
            if (empty($disc['ceasedDate'])) {
                $data['discCount']++;
            }
        }

        return array('data' => $data);
    }

    /**
     *
     * @param type $data
     * @param type $service
     */
    public function actionSave($data, $service = null)
    {
        $additionalDiscCount = $data['data']['additionalDiscs'];

        $this->requestDiscs($additionalDiscCount, array('licence' => $data['data']['id']));
    }

    /**
     * Override the save method, as we never save anything from this section
     *
     * @param array $data
     * @param string $service
     */
    public function save($data, $service = null)
    {

    }

    /**
     * Get disc number from a disc array
     *
     * @param array $disc
     * @return string
     */
    protected function getDiscNumberFromDisc($disc)
    {
        if (isset($disc['discNo']) && !empty($disc['discNo'])) {
            return $disc['discNo'];
        }

        if (empty($disc['issuedDate']) && empty($disc['ceasedDate'])) {
            return 'Pending';
        }

        return '';
    }

    /**
     * Alter form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterForm(Form $form)
    {
        $this->disableElements($form->get('data'));

        return $form;
    }

    /**
     * Alter action form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterActionForm(Form $form)
    {
        $form->get('form-actions')->remove('addAnother');

        return $form;
    }

    /**
     * Load the data for the multiple replace confirmation form
     *
     * @param string $id
     * @return array
     */
    public function replaceLoad($id)
    {
        return $this->genericMultipleActionLoad($id);
    }

    /**
     * Replace the selected discs
     *
     * @param array $data
     */
    public function replaceSave($data)
    {
        $ids = explode(',', $data['data']['id']);
        $this->ceaseDiscs($ids);
        $this->requestDiscs(count($ids), array('isCopy' => 'Y', 'licence' => $this->getIdentifier()));
    }

    /**
     * Load the data for the multiple void confirmation form
     *
     * @param string $id
     * @return array
     */
    public function voidLoad($id)
    {
        return $this->genericMultipleActionLoad($id);
    }

    /**
     * Void multiple discs
     *
     * @param array $data
     */
    public function voidSave($data)
    {
        $ids = explode(',', $data['data']['id']);

        $this->ceaseDiscs($ids);
    }

    /**
     * Request multiple discs
     *
     * @param int $count
     * @param array $data
     */
    protected function requestDiscs($count, $data = array())
    {
        $defaults = array(
            'isCopy' => 'N'
        );

        $postData = $this->getHelperService('DataHelper')->arrayRepeat(array_merge($defaults, $data), $count);

        $postData['_OPTIONS_'] = array('multiple' => true);

        $this->getHelperService('RestHelper')->makeRestCall('PsvDisc', 'POST', $postData);
    }

    /**
     * Cease multiple discs
     *
     * @param array $ids
     */
    protected function ceaseDiscs($ids)
    {
        $ceasedDate = date('Y-m-d');
        $postData = array();

        foreach ($ids as $id) {

            $postData[] = array(
                'id' => $id,
                'ceasedDate' => $ceasedDate,
                '_OPTIONS_' => array('force' => true)
            );
        }

        $postData['_OPTIONS_']['multiple'] = true;

        $this->getHelperService('RestHelper')->makeRestCall('PsvDisc', 'PUT', $postData);
    }
}
