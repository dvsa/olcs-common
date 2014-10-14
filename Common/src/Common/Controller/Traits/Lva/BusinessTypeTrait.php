<?php

/**
 * Shared logic between Business type controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Shared logic between Business type controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait BusinessTypeTrait
{

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
            $data = $this->formatDataForForm($this->getEntityService('Organisation')->getType($orgId));
        }

        $form = $this->getBusinessTypeForm()->setData($data);

        if ($request->isPost() && $form->isValid()) {
            $this->getEntityService('Organisation')->save($this->formatDataForSave($orgId, $data));

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
        return array(
            'id' => $orgId,
            'version' => $data['version'],
            'type' => $data['data']['type']
        );
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
