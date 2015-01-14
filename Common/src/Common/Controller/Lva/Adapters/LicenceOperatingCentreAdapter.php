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
     * Add variation info message
     */
    public function addMessages()
    {
        $params = [
            'licence' => $this->getLicenceAdapter()->getIdentifier()
        ];

        $link = $this->getController()->url()->fromRoute('create_variation', $params);
        $message = $this->getServiceLocator()->get('Helper\Translation')
            ->translateReplace('variation-application-message', [$link]);

        $this->getServiceLocator()->get('Helper\FlashMessenger')
            ->addCurrentMessage($message, 'info');
    }

    /**
     * Generic licence action form alterations
     *
     * @param \Zend\Form\Form $form
     */
    public function alterActionForm(Form $form)
    {
        $form = parent::alterActionForm($form);

        $filter = $form->getInputFilter();

        $hasVehicleElement = $filter->get('data')->has('noOfVehiclesRequired');
        $hasTrailerElement = $filter->get('data')->has('noOfTrailersRequired');

        if ($hasVehicleElement || $hasTrailerElement) {
            $data = $this->getEntityService()->getVehicleAuths(
                $this->getController()->params('child_id')
            );
        }

        if ($hasVehicleElement) {
            $this->attachCantIncreaseValidator(
                $filter->get('data')->get('noOfVehiclesRequired'),
                'vehicles',
                $data['noOfVehiclesRequired']
            );
        }

        if ($hasTrailerElement) {
            $this->attachCantIncreaseValidator(
                $filter->get('data')->get('noOfTrailersRequired'),
                'trailers',
                $data['noOfTrailersRequired']
            );
        }

        return $form;
    }

    /**
     * Disable conditional validation
     *
     * For licences, we don't want to validate any auth
     * totals if they haven't been altered
     */
    public function disableConditionalValidation(Form $form)
    {
        $postData = (array)$this->getController()->getRequest()->getPost();
        $postData = isset($postData['data']) ? $postData['data'] : [];

        // allow for *all* totals to have been submitted; in reality
        // the values will be a subset of this dependent on goods/psv
        $submitted = [
            'totAuthLargeVehicles',
            'totAuthMediumVehicles',
            'totAuthSmallVehicles',
            'totAuthVehicles',
            'totAuthTrailers'
        ];

        // we need to fetch our entity details and
        // as long as all relevant totals match, disable their
        // validation

        $totals = $this->getTotalAuthorisationsForLicence($this->getIdentifier());

        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $filter = $form->getInputFilter()->get('data');

        foreach ($submitted as $property) {
            if (isset($postData[$property]) && (int)$postData[$property] === (int)$totals[$property]) {
                $formHelper->disableValidation(
                    $filter->get($property)
                );
            }
        }
    }

    /**
     * Attach a can't increase validator
     *
     * @param Input $input
     * @param string $messageSuffix
     * @param int $previousValue
     */
    protected function attachCantIncreaseValidator($input, $messageSuffix, $previousValue)
    {
        $validatorChain = $input->getValidatorChain();

        $cantIncreaseValidator = $this->getServiceLocator()->get('CantIncreaseValidator');

        $licenceId = $this->getLicenceAdapter()->getIdentifier();

        $link = $this->getController()->url()->fromRoute('create_variation', ['licence' => $licenceId]);

        $message = $this->getServiceLocator()->get('Helper\Translation')
            ->translateReplace('cant-increase-' . $messageSuffix, [$link]);

        $cantIncreaseValidator->setGenericMessage($message);
        $cantIncreaseValidator->setPreviousValue($previousValue);

        $validatorChain->attach($cantIncreaseValidator);
    }

    /**
     * Get total authorisations for licence
     *
     * @param int $id
     * @return array
     */
    protected function getTotalAuthorisationsForLicence($id)
    {
        return $this->getLvaEntityService()->getTotalAuths($id);
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

        $data = $this->getTotalAuthorisationsForLicence($this->getIdentifier());

        $filter = $form->getInputFilter();

        foreach (['vehicles', 'trailers'] as $which) {
            $key = 'totAuth' . ucfirst($which);

            if ($filter->get('data')->has($key)) {
                $this->attachCantIncreaseValidator(
                    $filter->get('data')->get($key),
                    'total-' . $which,
                    $data[$key]
                );
            }
        }

        if ($form->get('data')->has('totCommunityLicences')) {
            $formHelper = $this->getServiceLocator()->get('Helper\Form');
            $formHelper->remove($form, 'data->totCommunityLicences');
        }

        return $form;
    }
}
