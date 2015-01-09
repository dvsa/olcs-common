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
    protected function alterForm(Form $form)
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

    /**
     * Add variation info message
     */
    public function addMessages()
    {
        $params = [
            'licence' => $this->getLicenceAdapter()->getIdentifier()
        ];

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentMessage(
            $this->getServiceLocator()->get('Helper\Translation')->formatTranslation(
                '%s <a href="' . $this->getController()->url()->fromRoute('create_variation', $params) . '">%s</a>',
                array(
                    'variation-application-text',
                    'variation-application-link-text'
                )
            ),
            'info'
        );
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

        $data = $this->getVehicleAuthsForOperatingCentre(
            $this->getController()->params('child_id')
        );

        if ($filter->get('data')->has('noOfVehiclesRequired')) {
            $this->attachCantIncreaseValidator(
                $filter->get('data')->get('noOfVehiclesRequired'),
                'vehicles',
                $data['noOfVehiclesRequired']
            );
        }

        if ($filter->get('data')->has('noOfTrailersRequired')) {
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
        $data = $this->getController()->getRequest()->getPost();
        $data = isset($data['data']) ? $data['data'] : [];

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
            if (isset($data[$property]) && (int)$data[$property] === (int)$totals[$property]) {
                $formHelper->disableValidation(
                    $filter->get($property)
                );
            }
        }
    }

    /**
     * Format crud data for save
     *
     * @param array $data
     */
    protected function formatCrudDataForSave($data)
    {
        $data = parent::formatCrudDataForSave($data);

        unset($data['operatingCentre']['addresses']);

        return $data;
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
     * Get the vehicle auths for the OC (given a licence_operating_Centre_id)
     *
     * @param int $id
     * @return array
     */
    protected function getVehicleAuthsForOperatingCentre($id)
    {
        return $this->getServiceLocator()->get('Entity\LicenceOperatingCentre')->getVehicleAuths($id);
    }

    /**
     * Get total authorisations for licence
     *
     * @param int $id
     * @return array
     */
    protected function getTotalAuthorisationsForLicence($id)
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getTotalAuths($id);
    }
}
