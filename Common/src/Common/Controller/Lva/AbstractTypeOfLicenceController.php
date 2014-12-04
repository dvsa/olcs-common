<?php

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface;
use Zend\Http\Response;

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicenceController extends AbstractController
implements Interfaces\TypeOfLicenceAdapterAwareInterface
{
    protected $typeOfLicenceAdapter;

    /**
     * @return TypeOfLicenceAdapterInterface
     */
    public function getTypeOfLicenceAdapter()
    {
        return $this->typeOfLicenceAdapter;
    }

    public function setTypeOfLicenceAdapter(TypeOfLicenceAdapterInterface $adapter)
    {
        $this->typeOfLicenceAdapter = $adapter;
    }

    /**
     * Type of licence section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = (array)$request->getPost();

            $response = $this->processPostAdapter($data);

            if ($response instanceof Response) {
                return $response;
            }

        } else {
            $data = $this->formatDataForForm($this->getTypeOfLicenceData());
        }

        $form = $this->getTypeOfLicenceForm()->setData($data);

        if ($request->isPost() && $form->isValid()) {

            $data = $this->formatDataForSave($data);

            $data['id'] = $this->getIdentifier();

            $this->getLvaEntityService()->save($data);

            $this->postSave('type_of_licence');

            return $this->completeSection('type_of_licence');
        }

        $this->getServiceLocator()->get('Script')->loadFile('type-of-licence');

        return $this->render('type_of_licence', $form);
    }

    protected function processPostAdapter($data)
    {
        $adapter = $this->getTypeOfLicenceAdapter();

        if ($adapter === null) {
            return;
        }

        if ($adapter->doesChangeRequireConfirmation($data['type-of-licence'], $this->getTypeOfLicenceData())) {

            return $this->redirect()->toRoute(null, $adapter->getRouteParams(), $adapter->getQueryParams(), true);
        }

        if ($adapter->processChange($data['type-of-licence'], $this->getTypeOfLicenceData())) {
            return $this->completeSection('tyoe_of_licence');
        }
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
