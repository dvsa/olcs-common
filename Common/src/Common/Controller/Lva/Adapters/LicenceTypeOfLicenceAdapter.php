<?php

/**
 * Licence Type Of Licence Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Entity\LicenceEntityService;

/**
 * Licence Type Of Licence Adapter
 * @NOTE This is a CONTROLLER adapter and thus contains logic similar to that of a controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTypeOfLicenceAdapter extends AbstractTypeOfLicenceAdapter
{
    protected $confirmationMessage = 'create-variation-confirmation';
    protected $extraConfirmationMessage = 'licence_type_of_licence_confirmation';

    public function doesChangeRequireConfirmation(array $postData, array $currentData)
    {
        $this->queryParams = $postData;

        return $this->queryParams['licence-type'] !== $currentData['licenceType'];
    }

    public function alterForm(\Zend\Form\Form $form, $id = null, $applicationType = null)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        // Generic alteration
        $form->get('form-actions')->get('save')->setLabel('save');

        $typeOfLicenceFieldset = $form->get('type-of-licence');

        // Change labels
        $typeOfLicenceFieldset->get('operator-location')->setLabel('operator-location');
        $typeOfLicenceFieldset->get('operator-type')->setLabel('operator-type');
        $typeOfLicenceFieldset->get('licence-type')->setLabel('licence-type');

        // Add padlocks
        $formHelper->lockElement($typeOfLicenceFieldset->get('operator-location'), 'operator-location-lock-message');
        $formHelper->lockElement($typeOfLicenceFieldset->get('operator-type'), 'operator-type-lock-message');

        // Disable elements
        $formHelper->disableElement($form, 'type-of-licence->operator-location');
        $formHelper->disableElement($form, 'type-of-licence->operator-type');

        // Optional disable and lock type of licence
        if ($this->shouldDisableLicenceType($id, $applicationType)) {
            // Disable and lock type of licence
            $formHelper->disableElement($form, 'type-of-licence->licence-type');
            $formHelper->lockElement($typeOfLicenceFieldset->get('licence-type'), 'licence-type-lock-message');

            // Disable buttons
            $formHelper->disableElement($form, 'form-actions->save');
        }

        $typeOfLicence = $this->getServiceLocator()->get('Entity\Licence')->getTypeOfLicenceData($id);

        if ($typeOfLicence['goodsOrPsv'] === LicenceEntityService::LICENCE_CATEGORY_PSV
            && $typeOfLicence['licenceType'] !== LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
        ) {
            $formHelper->removeOption(
                $typeOfLicenceFieldset->get('licence-type'),
                LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
            );
        }

        return $form;
    }

    public function setMessages($id = null, $applicationType = null)
    {
        $flashMessenger = $this->getServiceLocator()->get('Helper\FlashMessenger');

        if ($this->shouldDisableLicenceType($id, $applicationType)) {
            $flashMessenger->addCurrentInfoMessage('variation-application-text3');
        } else {
            // If some fields are editable
            $translationHelper = $this->getServiceLocator()->get('Helper\Translation');

            $message = $translationHelper->formatTranslation(
                '%s <a href="%s" target="_blank">%s</a>',
                array(
                    'variation-application-text2',
                    // @todo replace with real link
                    'https://www.google.co.uk/?q=Licence+Type#q=Licence+Type',
                    'variation-application-link-text'

                )
            );
            $flashMessenger->addCurrentInfoMessage($message);
        }
    }

    public function confirmationAction()
    {
        $request = $this->getController()->getRequest();

        if ($request->isPost()) {

            $data = [
                'licenceType' => $this->getController()->params()->fromQuery('licence-type')
            ];

            $licenceId = $this->getController()->params('licence');

            $appId = $this->getServiceLocator()->get('Entity\Application')->createVariation($licenceId, $data);

            $this->getServiceLocator()->get('Processing\VariationSection')
                ->completeSection($appId, 'type_of_licence');

            return $this->getController()->redirect()->toRouteAjax('lva-variation', ['application' => $appId]);
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('GenericConfirmation');
        $formHelper->setFormActionFromRequest($form, $this->getController()->getRequest());

        $form->get('form-actions')->get('submit')->setLabel('create-variation-button');

        return $form;
    }

    public function shouldDisableLicenceType($id, $applicationType = null)
    {
        $typeOfLicence = $this->getServiceLocator()->get('Entity\Licence')->getTypeOfLicenceData($id);

        if ($typeOfLicence['goodsOrPsv'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            return false;
        }

        $enabled = [
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ];

        if (in_array($typeOfLicence['licenceType'], $enabled)) {
            return false;
        }

        if ($typeOfLicence['licenceType'] === LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            return true;
        }

        if ($typeOfLicence['licenceType'] === LicenceEntityService::LICENCE_TYPE_RESTRICTED
            && $applicationType === 'external') {
            return true;
        }

        return false;
    }
}
