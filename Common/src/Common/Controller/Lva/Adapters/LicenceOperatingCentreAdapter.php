<?php

/**
 * Licence Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;

/**
 * Licence Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentreAdapter extends AbstractOperatingCentreAdapter
{
    protected $lva = 'licence';

    protected $entityService = 'Entity\LicenceOperatingCentre';

    /**
     * Get extra document properties to save
     *
     * @return array
     */
    public function getDocumentProperties()
    {
        return array(
            'licence' => $this->getLicenceAdapter()->getIdentifier()
        );
    }

    /**
     * Alter the form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterForm(Form $form)
    {
        $form = parent::alterForm($form);

        if ($form->get('data')->has('totCommunityLicences')) {
            $formHelper = $this->getServiceLocator()->get('Helper\Form');
            $formHelper->disableElement($form, 'data->totCommunityLicences');
            $formHelper->disableValidation($form->getInputFilter()->get('data')->get('totCommunityLicences'));

            $formHelper->lockElement(
                $form->get('data')->get('totCommunityLicences'),
                'community-licence-changes-contact-office'
            );
        }

        $form->get('table')->get('table')->getTable()->removeAction('schedule41');

        return $form;
    }

    /**
     * Format data for save
     *
     * @param array $data
     * @return array
     */
    protected function formatDataForSave(array $data)
    {
        $data = parent::formatDataForSave($data);

        unset($data['totCommunityLicences']);

        return $data;
    }

    /**
     * Format data for form
     *
     * @param array $data
     * @param array $tableData
     * @param array $licenceData
     * @return array
     */
    protected function formatDataForForm(array $data, array $tableData, array $licenceData)
    {
        $formData = parent::formatDataForForm($data, $tableData, $licenceData);

        if (isset($data['enforcementArea']['id'])) {
            $formData['dataTrafficArea']['enforcementArea'] = $data['enforcementArea']['id'];
        }

        return $formData;
    }
}
