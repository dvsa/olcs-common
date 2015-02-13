<?php

/**
 * Abstract Conditions Undertakings Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Common\Service\Entity\ConditionUndertakingEntityService;
use Common\Controller\Lva\Interfaces\ConditionsUndertakingsAdapterInterface;
use Common\Service\Table\Formatter\Address;
use Common\Service\Table\TableBuilder;

/**
 * Abstract Conditions Undertakings Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractConditionsUndertakingsAdapter extends AbstractAdapter implements
    ConditionsUndertakingsAdapterInterface
{
    protected $tableName = 'lva-conditions-undertakings';

    /**
     * Get licence id from the given lva id
     *
     * @param int id
     * @return int
     */
    abstract protected function getLicenceId($id);

    /**
     * Get the LVA operating centre entity service
     *
     * @return \Common\Service\Entity\AbstractEntity
     */
    abstract protected function getLvaOperatingCentreEntityService();

    /**
     * Attach the relevant scripts to the main page
     */
    public function attachMainScripts()
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');
    }

    /**
     * Delete a record
     *
     * @param int $id
     * @param int $parentId
     */
    public function delete($id, $parentId)
    {
        $this->getServiceLocator()->get('Entity\ConditionUndertaking')->delete($id);
    }

    /**
     * Check whether we can update the record
     *
     * @param int $id
     * @param int $parentId
     * @return bool
     */
    public function canEditRecord($id, $parentId)
    {
        return true;
    }

    /**
     * Remove the restore button
     *
     * @param TableBuilder $table
     */
    public function alterTable(TableBuilder $table)
    {
        $table->removeAction('restore');
    }

    /**
     * Save the data
     *
     * @param array $data
     * @return int
     */
    public function save($data)
    {
        $response = $this->getServiceLocator()->get('Entity\ConditionUndertaking')->save($data);

        if (isset($response['id'])) {
            return $response['id'];
        }

        return $data['id'];
    }

    /**
     * Process the data for saving
     *
     * @param array $data
     * @param int $id
     * @return array
     */
    public function processDataForSave($data, $id)
    {
        if ($data['fields']['attachedTo'] == ConditionUndertakingEntityService::ATTACHED_TO_LICENCE) {
            $data['fields']['operatingCentre'] = null;
        } else {
            $data['fields']['operatingCentre'] = $data['fields']['attachedTo'];
            $data['fields']['attachedTo'] = ConditionUndertakingEntityService::ATTACHED_TO_OPERATING_CENTRE;
        }

        return $data;
    }

    /**
     * Process the data for the form
     *
     * @param array $data
     * @return array
     */
    public function processDataForForm($data)
    {
        if (isset($data['fields']['attachedTo'])
            && $data['fields']['attachedTo'] != ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
        ) {

            $data['fields']['attachedTo'] =
                isset($data['fields']['operatingCentre']) ? $data['fields']['operatingCentre'] : '';
        }

        return $data;
    }

    /**
     * Set the attached to options for the form, based on the lva type and id
     *
     * @param Form $form
     * @param int $id
     */
    public function alterForm(Form $form, $id)
    {
        $licenceId = $this->getLicenceId($id);

        $licNo = $this->getLicenceNumber($licenceId);

        $options = array(
            'Licence' => array(
                'label' => 'Licence',
                'options' => array(
                    ConditionUndertakingEntityService::ATTACHED_TO_LICENCE => 'Licence (' . $licNo . ')'
                )
            )
        );

        $operatingCentres = $this->getOperatingCentresForList($id);
        $attachedToOperatingCentres = [];

        foreach ($operatingCentres as $oc) {
            $attachedToOperatingCentres[$oc['id']] = Address::format($oc['address']);
        }

        if (!empty($attachedToOperatingCentres)) {
            $options['OC'] = array(
                'label' => 'OC Address',
                'options' => $attachedToOperatingCentres
            );
        }

        $form->get('fields')->get('attachedTo')->setValueOptions($options);
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Each LVA section must implement this method
     *
     * @param int id
     * @returna array
     */
    protected function getOperatingCentresForList($id)
    {
        $results = $this->getLvaOperatingCentreEntityService()
            ->getOperatingCentreListForLva($id);

        $oc = [];

        foreach ($results['Results'] as $loc) {
            $oc[] = $loc['operatingCentre'];
        }

        return $oc;
    }

    /**
     * Grab the licence number from the licence id
     *
     * @param int $licenceId
     * @return string
     */
    protected function getLicenceNumber($licenceId)
    {
        $licence = $this->getServiceLocator()->get('Entity\Licence')->getById($licenceId);

        return $licence['licNo'];
    }
}
