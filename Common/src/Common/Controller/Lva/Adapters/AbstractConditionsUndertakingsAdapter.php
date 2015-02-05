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

/**
 * Abstract Conditions Undertakings Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractConditionsUndertakingsAdapter extends AbstractAdapter implements
    ConditionsUndertakingsAdapterInterface
{
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
     * Save the data
     *
     * @param array $data
     * @return int
     */
    public function save($data)
    {

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
        unset($data['fields']['licence']);

        if ($data['fields']['attachedTo'] == ConditionUndertakingEntityService::ATTACHED_TO_LICENCE) {
            $data['fields']['operatingCentre'] = null;
            $data['fields']['attachedTo'] = ConditionUndertakingEntityService::ATTACHED_TO_LICENCE;
        } else {
            $data['fields']['operatingCentre'] = $data['fields']['attachedTo'];
            $data['fields']['attachedTo'] = ConditionUndertakingEntityService::ATTACHED_TO_OPERATING_CENTRE;
        }

        return $data;
    }

    /**
     * Persist the condition
     *
     * @param array $data
     * @return int
     */
    protected function persistConditionUndertaking($data)
    {
        $response = $this->getServiceLocator()->get('Entity\ConditionUndertaking')->save($data);

        if (isset($response['id'])) {
            return $response['id'];
        }

        return $data['id'];
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

        $form->get('fields')
            ->get('attachedTo')
            ->setValueOptions($options);
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
