<?php

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicenceController extends AbstractController
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

            $this->getServiceLocator()->get('Entity\Licence')->save($data);

            $this->postSave('type_of_licence');

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
    protected function formatDataForSave($data)
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
    protected function formatDataForForm($data)
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
    protected function getTypeOfLicenceForm()
    {
        $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\TypeOfLicence');

        $this->alterFormForLocation($form);
        $this->alterFormForLva($form);

        return $form;
    }
}
