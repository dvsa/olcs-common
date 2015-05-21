<?php

/**
 * Shared logic between Business type controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Interfaces\AdapterAwareInterface;

/**
 * Shared logic between Business type controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractBusinessTypeController extends AbstractController implements AdapterAwareInterface
{
    use Traits\AdapterAwareTrait;

    /**
     * Business type section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        $orgId = $this->getCurrentOrganisationId();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($this->getServiceLocator()->get('Entity\Organisation')->getType($orgId));
        }

        $form = $this->getBusinessTypeForm()->setData($data);

        $this->alterFormForLva($form);

        $this->getAdapter()->alterFormForOrganisation($form, $orgId);

        if ($request->isPost() && $form->isValid()) {
            $this->getServiceLocator()->get('Entity\Organisation')->save($this->formatDataForSave($orgId, $data));

            $this->postSave('business_type');

            return $this->completeSection('business_type');
        }

        return $this->render('business_type', $form);
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
            'data' => array(
                'type' => $data['type']['id']
            )
        );
    }

    /**
     * Format data for save
     *
     * @param int $orgId
     * @param array $data
     * @return array
     */
    private function formatDataForSave($orgId, $data)
    {
        $persist = array(
            'id' => $orgId,
            'version' => $data['version']
        );

        // the business type input might be disabled; only update
        // if we actually get it through
        if (isset($data['data']['type'])) {
            $persist['type'] = $data['data']['type'];
        }

        return $persist;
    }

    /**
     * Get business type form
     *
     * @return \Zend\Form\Form
     */
    private function getBusinessTypeForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\BusinessType');
    }
}
