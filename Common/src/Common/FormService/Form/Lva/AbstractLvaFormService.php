<?php

namespace Common\FormService\Form\Lva;

use Common\Form\Elements\InputFilters\Lva\BackToApplicationActionLink;
use Common\Form\Elements\InputFilters\Lva\BackToLicenceActionLink;
use Common\Form\Elements\InputFilters\Lva\BackToVariationActionLink;
use Common\FormService\Form\AbstractFormService;
use Common\Form\Elements\InputFilters\ActionLink;
use Common\RefData;

/**
 * Abstract Lva Form Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLvaFormService extends AbstractFormService
{
    protected $backToLinkMap = [
        'application' => BackToApplicationActionLink::class,
        'licence' => BackToLicenceActionLink::class,
        'variation' => BackToVariationActionLink::class
    ];

    protected function addBackToOverviewLink($form, $lva, $isPrimary = true)
    {
        $backToOverviewClass = $this->backToLinkMap[$lva];
        /** @var ActionLink $back */
        $back = new $backToOverviewClass();

        if (!$isPrimary) {
            $back->setAttribute('class', 'govuk-button govuk-button--secondary');
        }

        $form->get('form-actions')->add($back);
    }

    protected function removeStandardFormActions($form)
    {
        $this->removeFormAction($form, 'save');
        $this->removeFormAction($form, 'saveAndContinue');
        $this->removeFormAction($form, 'cancel');
    }

    protected function removeFormAction($form, $action)
    {
        if (!$form->has('form-actions')) {
            return;
        }

        $formActions = $form->get('form-actions');

        if ($formActions->has($action)) {
            $formActions->remove($action);
        }
    }

    protected function setPrimaryAction($form, $action)
    {
        if (!$form->has('form-actions')) {
            return;
        }

        $formActions = $form->get('form-actions');

        if (!$formActions->has($action)) {
            return;
        }

        $formActions->get($action)->setAttribute('class', 'govuk-button');
    }

    /**
     * Return true if the current internal user has read only permissions
     *
     * @return bool
     */
    protected function isInternalReadOnly()
    {
        $authService = $this->getServiceLocator()->get(\ZfcRbac\Service\AuthorizationService::class);
        return (
            $authService->isGranted(RefData::PERMISSION_INTERNAL_USER)
            && !$authService->isGranted(RefData::PERMISSION_INTERNAL_EDIT)
        );
    }
}
