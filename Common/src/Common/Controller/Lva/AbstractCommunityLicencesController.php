<?php

/**
 * Shared logic between Community Licences controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Shared logic between Community Licences controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractCommunityLicencesController extends AbstractController
{
    protected $section = 'community_licences';

    /**
     * Community Licences section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->postSave('community_licences');
            return $this->completeSection('community_licences');
        }

        $form = $this->getForm();

        $this->alterFormForLocation($form);
        $this->alterFormForLva($form);

        return $this->render('community_licences', $form);
    }

    /**
     * Get form
     *
     * @return \Zend\Form\Form
     */
    private function getForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\CommunityLicences');
    }
}
