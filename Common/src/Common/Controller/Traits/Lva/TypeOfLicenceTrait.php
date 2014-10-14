<?php

/**
 * Shared logic between Type Of Licence controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Shared logic between Type Of Licence controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait TypeOfLicenceTrait
{
    /**
     * Type of licence section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($this->getTypeOfLicenceData());
        }

        $form = $this->getTypeOfLicenceForm()->setData($data);

        if ($request->isPost() && $form->isValid()) {

            $licenceId = $this->getLicenceId();

            $data = $this->formatDataForSave($data);

            $data['id'] = $licenceId;

            $this->getEntityService('Licence')->save($data);

            return $this->completeSection('type_of_licence');
        }

        $this->getServiceLocator()->get('Script')->loadFile('type-of-licence');

        return $this->render('type_of_licence', $form);
    }

    /**
     * Format data for save
     *
     * @param array $data
     * @return array
     */
    private function formatDataForSave($data)
    {
        return array(
            'version' => $data['version'],
            'niFlag' => $data['type-of-licence']['operator-location'],
            'goodsOrPsv' => $data['type-of-licence']['operator-type'],
            'licenceType' => $data['type-of-licence']['licence-type']
        );
    }

    /**
     * Format data for form
     *
     * @param array $data
     * @return array
     */
    private function formatDataForForm($data)
    {
        return array(
            'version' => $data['version'],
            'type-of-licence' => array(
                'operator-location' => $data['niFlag'],
                'operator-type' => $data['goodsOrPsv'],
                'licence-type' => $data['licenceType']
            )
        );
    }

    /**
     * Get type of licence form
     *
     * @return \Zend\Form\Form
     */
    private function getTypeOfLicenceForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\TypeOfLicence');
    }
}
