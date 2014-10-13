<?php

/**
 * Shared logic between Addresses controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Shared logic between Addresses controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait AddressesTrait
{
    /**
     * Addresses section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $addressData = array();

            $data = $this->formatDataForForm($addressData);
        }

        $form = $this->getAddressesForm()->setData($data);

        if (!$this->getHelperService('FormHelper')->processAddressLookup($form, $request)
            && $request->isPost() && $form->isValid()
        ) {

            // Save changes


            return $this->completeSection('addresses');
        }

        return $this->render('addresses', $form);
    }

    /**
     * Format data for save
     *
     * @param array $data
     * @return array
     */
    private function formatDataForSave($data)
    {
        return array();
    }

    /**
     * Format data for form
     *
     * @param array $data
     * @return array
     */
    private function formatDataForForm($data)
    {
        return array();
    }

    /**
     * Get the form
     *
     * @return \Zend\Form\Form
     */
    private function getAddressesForm()
    {
        return $this->getHelperService('FormHelper')->createForm('Lva\Addresses');
    }
}
