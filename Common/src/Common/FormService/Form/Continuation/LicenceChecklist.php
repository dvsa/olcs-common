<?php

namespace Common\FormService\Form\Continuation;

use Common\FormService\Form\AbstractFormService;
use Common\Service\Helper\FormHelperService;
use Common\Form\Model\Form\Continuation\LicenceChecklist as LicenceChecklistForm;
use Common\Form\Form;
use Common\RefData;

/**
 * Continuation licence checklist form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceChecklist extends AbstractFormService
{
    protected $checklistCheckboxes = [
        'typeOfLicence',
        'businessType',
        'businessDetails',
        'addresses',
        'people',
        'operatingCentres',
        'transportManagers',
        'vehicles',
        'safety',
        'conditionsUndertakings',
    ];

    /**
     * Get form
     *
     * @param array $data continuation detail data
     *
     * @return Form
     */
    public function getForm($data)
    {
        $form = $this->getFormHelper()->createForm(LicenceChecklistForm::class);

        $this->alterForm($form, $data);

        return $form;
    }

    /**
     * Alter form
     *
     * @param Form  $form form
     * @param array $data data
     *
     * @return void
     */
    protected function alterForm($form, $data)
    {
        $orgData = $data['licence']['organisation'];
        $this->alterPeopleSection($form, $orgData['type']['id']);
        $this->alterBackUrl($form, $data['licence']['id']);
        $this->alterAllSections($form, $data['sections']);
    }

    /**
     * Alter people section
     *
     * @param Form   $form    form
     * @param string $orgType organisation type
     *
     * @return void
     */
    protected function alterPeopleSection($form, $orgType)
    {
        $dataFieldset = $form->get('data');
        $peopleCheckbox = $dataFieldset->get('peopleCheckbox');
        $peopleCheckbox->setLabel(
            $peopleCheckbox->getLabel() . $orgType
        );
        $peopleCheckbox->setOption('not_checked_message', $peopleCheckbox->getOption('not_checked_message') . $orgType);
    }

    /**
     * Alter back url
     *
     * @param Form $form      form
     * @param int  $licenceId licence id
     *
     * @return void
     */
    protected function alterBackUrl($form, $licenceId)
    {
        $backButton = $form->get('data')->get('licenceChecklistConfirmation')->get('noContent')->get('backToLicence');
        $backButton->setValue(
            $this->getServiceLocator()->get('Helper\Url')->fromRoute(
                'lva-licence',
                ['licence' => $licenceId]
            )
        );
    }

    /**
     * Alter all sections
     *
     * @param Form  $form     form
     * @param array $sections sections
     *
     * @rtuen void
     */
    protected function alterAllSections($form, $sections)
    {
        $formHelper = $this->getFormHelper();
        foreach ($this->checklistCheckboxes as $checkbox) {
            if (!in_array($checkbox, $sections)) {
                $formHelper->remove($form, 'data->' . $checkbox . 'Checkbox');
            }
        }
    }
}