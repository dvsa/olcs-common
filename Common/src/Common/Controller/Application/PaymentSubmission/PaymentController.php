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
     * @param string $service
     */
    protected function save($data, $service = null)
    {
        // Update the application status to "Under Consideration"
        $bundle = array(
            'properties' => array(
                'id',
                'version'
            ),
            'children' => array(
                'licence' => array(
                    'properties' => array(
                        'id'
                    )
                )
            )
        );
        $application = $this->makeRestCall('Application', 'GET', array('id' => $this->getIdentifier()), $bundle);
        $application['status']='apsts_consideration';
        $this->makeRestCall('Application', 'PUT', $application);

        // Create a task - OLCS-3297
        $task = array(
            'category' => 9,
            'taskSubCategory' => 16,
            'description' => 'GV79 Application',
            'isClosed' => 0,
            'application' => $application['id'],
            'licence' => $application['licence']['id']
        );
        $this->makeRestCall('Task', 'POST', $task);

    }
}
