<?php

/**
 * External Licence Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service\OperatingCentre;

use Zend\Form\Form;

/**
 * External Licence Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ExternalLicenceAuthorisationSectionService extends AbstractLicenceAuthorisationSectionService
{
    /**
     * Operating centre address bundle
     *
     * @var array
     */
    private $operatingCentreAddressBundle = array(
        'properties' => array(),
        'children' => array(
            'operatingCentre' => array(
                'properties' => array(),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    /**
     * Alter action form
     *
     * @param \Zend\Form\Form $form
     */
    public function alterActionForm(Form $form)
    {
        $form = parent::alterActionForm($form);

        $addressElement = $form->get('address');

        $this->disableElements($addressElement);
        $this->disableValidation($form->getInputFilter()->get('address'));

        $lockedElements = array(
            $addressElement->get('searchPostcode'),
            $addressElement->get('addressLine1'),
            $addressElement->get('town'),
            $addressElement->get('postcode'),
            $addressElement->get('countryCode'),
        );

        foreach ($lockedElements as $element) {
            $this->lockElement($element, 'operating-centre-address-requires-variation');
        }

        return $form;
    }

    /**
     * Post set form data
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function postSetFormData(Form $form)
    {
        if ($this->isAction()) {
            $postData = (array)$this->getRequest()->getPost();

            $addressData = $this->getOperatingCentreAddressData($this->getActionId());

            $postData['address'] = $addressData;
            $postData['address']['countryCode'] = $postData['address']['countryCode']['id'];

            $form->setData($postData);
        }

        return $form;
    }

    /**
     * Get operating centre address data from a licence_operating_centre id
     *
     * @param int $id
     * @return array
     */
    protected function getOperatingCentreAddressData($id)
    {
        $data = $this->getHelperService('RestHelper')
            ->makeRestCall($this->getActionService(), 'GET', $id, $this->operatingCentreAddressBundle);

        return $data['operatingCentre']['address'];
    }

    /**
     * Remove the advertisements fieldset and the confirmation checkboxes
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForGoods(Form $form)
    {
        parent::alterActionFormForGoods($form);

        $form->remove('advertisements')
            ->get('data')
            ->remove('sufficientParking')
            ->remove('permission');
    }
}
