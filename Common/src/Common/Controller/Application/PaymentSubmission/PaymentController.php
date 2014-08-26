<?php

/**
 * Payment Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\PaymentSubmission;

/**
 * Payment Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PaymentController extends PaymentSubmissionController
{
    /**
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->renderSection();
    }

    protected function alterForm($form)
    {
        $form->get('form-actions')->get('submit')->setLabel('Pay and submit');

        return $form;
    }

    /**
     * Save method - no payment taken, simply updates to 'in progress'
     *
     * @param array $data
     * @parem string $service
     */
    protected function save($data, $service = null)
    {
        // Update the application status to "Under Consideration"
        $bundle = array(
            'properties' => array(
                'id',
                'version'
            )
        );
        $application = $this->makeRestCall('Application', 'GET', array('id' => $this->getIdentifier()), $bundle);
        $application['status']='apsts_consideration';
        $this->makeRestCall('Application', 'PUT', $application);
    }
}
