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
        $this->alterPeopleSection(
            $form,
            $orgData['type']['id'],
            count($orgData['organisationPersons']),
            $data['id']
        );

        $this->alterVehiclesSection($form, count($data['licence']['licenceVehicles']), $data['id']);
    }

    /**
     * Alter people section
     *
     * @param Form   $form                 form
     * @param string $orgType              organisation type
     * @param int    $personCount          person count
     * @param int    $continuationDetailId continuation detail id
     *
     * @return void
     */
    protected function alterPeopleSection($form, $orgType, $personCount, $continuationDetailId)
    {
        $dataFieldset = $form->get('data');
        $peopleCheckbox = $dataFieldset->get('peopleCheckbox');
        $peopleCheckbox->setLabel(
            $peopleCheckbox->getLabel() . $orgType
        );
        $viewButton = $dataFieldset->get('viewPeopleSection')->get('viewPeople');
        $viewButton->setLabel(
            $viewButton->getLabel() . $orgType
        );
        $formHelper = $this->getFormHelper();
        if ($personCount > RefData::CONTINUATIONS_DISPLAY_PERSON_COUNT) {
            $formHelper->remove($form, 'data->people');
            $viewButton->setValue(
                $this->getServiceLocator()->get('Helper\Url')->fromRoute(
                    'continuation/checklist/people',
                    [
                        'continuationDetailId' => $continuationDetailId,
                    ]
                )
            );
        } else {
            $formHelper->remove($form, 'data->viewPeopleSection');
        }
    }

    /**
     * Alter vehicles section
     *
     * @param Form $form                 form
     * @param int  $vehiclesCount        vehicles count
     * @param int  $continuationDetailId continuation detail id
     *
     * @return void
     */
    protected function alterVehiclesSection($form, $vehiclesCount, $continuationDetailId)
    {
        $formHelper = $this->getFormHelper();
        if ($vehiclesCount > RefData::CONTINUATIONS_DISPLAY_VEHICLES_COUNT) {
            $formHelper->remove($form, 'data->vehicles');
            $viewButton = $form->get('data')->get('viewVehiclesSection')->get('viewVehicles');
            $viewButton->setValue(
                $this->getServiceLocator()->get('Helper\Url')->fromRoute(
                    'continuation/checklist/vehicles',
                    [
                        'continuationDetailId' => $continuationDetailId,
                    ]
                )
            );
        } else {
            $formHelper->remove($form, 'data->viewVehiclesSection');
        }
    }
}
