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
        return $this->getHelperService('FormHelper')->createForm('Lva\BusinessType');
    }
}
