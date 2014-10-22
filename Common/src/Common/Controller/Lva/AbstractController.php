<?php

/**
 * Lva Abstract Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Zend\Form\Form;
use Common\Util;
use Common\Service\Data\SectionConfig;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Common\Exception\ResourceConflictException;

/**
 * Lva Abstract Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractController extends AbstractActionController
{
    use Util\FlashMessengerTrait;

    /**
     * Internal/External
     *
     * @var string
     */
    protected $location;

    /**
     * Licence/Variation/Application
     *
     * @var string
     */
    protected $lva;

    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            throw new Exception\DomainException('Missing route matches; unsure how to retrieve action');
        }

        $action = $routeMatch->getParam('action', 'not-found');
        $method = static::getMethodFromAction($action);

        if (!method_exists($this, $method)) {
            $method = 'notFoundAction';
        }

        if ($routeMatch->getParam('skipPreDispatch', false) || ($actionResponse = $this->preDispatch()) === null) {
            try {
                $actionResponse = $this->$method();
            } catch (ResourceConflictException $ex) {
                $this->addErrorMessage('version-conflict-message');
                $actionResponse = $this->reload();
            }
        }

        $e->setResult($actionResponse);

        return $actionResponse;
    }

    /**
     * Hook into the dispatch before the controller action is executed
     */
    protected function preDispatch()
    {

    }

    /**
     * Check if a button is pressed
     *
     * @param string $button
     * @return boolean
     */
    protected function isButtonPressed($button)
    {
        $data = (array)$this->getRequest()->getPost();

        return isset($data['form-actions'][$button]);
    }

    /**
     * Get accessible sections
     */
    protected function getAccessibleSections($keysOnly = true)
    {
        $licenceType = $this->getTypeOfLicenceData();

        $access = array(
            $this->location,
            $this->lva,
            $licenceType['goodsOrPsv'],
            $licenceType['licenceType']
        );

        $sectionConfig = new SectionConfig();
        $inputSections = $sectionConfig->getAll();

        $sections = $this->getServiceLocator()->get('Helper\Access')->setSections($inputSections)
            ->getAccessibleSections($access);

        if ($keysOnly) {
            $sections = array_keys($sections);
        }

        return $sections;
    }

    /**
     * Get licence type information
     *
     * @return array
     */
    protected function getTypeOfLicenceData()
    {
        throw new \Exception('getTypeOfLicenceData must be implemented');
    }

    /**
     * Wrapper method so we can extend this behaviour
     *
     * @return \Zend\Http\Response
     */
    protected function goToOverviewAfterSave($lvaId = null)
    {
        return $this->goToOverview($lvaId);
    }

    /**
     * Go to overview page
     *
     * @param int $lvaId
     * @return \Zend\Http\Response
     */
    protected function goToOverview($lvaId = null)
    {
        if ($lvaId === null) {
            $lvaId = $this->params('id');
        }

        return $this->redirect()->toRoute('lva-' . $this->lva, array('id' => $lvaId));
    }

    /**
     * Add the section updated message
     *
     * @param string $section
     */
    protected function addSectionUpdatedMessage($section)
    {
        $message = $this->getServiceLocator()->get('Helper\Translation')->formatTranslation(
            '%s %s',
            array(
                'section.name.' . $section,
                'section-updated-successfully-message-suffix'
            )
        );

        $this->addSuccessMessage($message);
    }

    /**
     * Redirect to the next section
     *
     * @param string $currentSection
     */
    protected function goToNextSection($currentSection)
    {
        $sections = $this->getAccessibleSections();

        $index = array_search($currentSection, $sections);

        // If there is no next section
        if (!isset($sections[$index + 1])) {
            return $this->goToOverview($this->getApplicationId());
        } else {
            return $this->redirect()
                ->toRoute('lva-' . $this->lva . '/' . $sections[$index + 1], array('id' => $this->getApplicationId()));
        }
    }

    /**
     * Check for redirect
     *
     * @param int $lvaId
     * @return null|\Zend\Http\Response
     */
    protected function checkForRedirect($lvaId)
    {
        if ($this->isButtonPressed('cancel')) {
            // If we are on a sub-section, we need to go back to the section
            if ($this->params('action') !== 'index') {
                return $this->redirect()->toRoute(
                    null,
                    array('id' => $lvaId)
                );
            }

            return $this->handleCancelRedirect($lvaId);
        }
    }

    /**
     * Handle cancel redirect is implemented differently internally than externally
     */
    abstract protected function handleCancelRedirect($lvaId);

    /**
     * No-op but extended
     */
    protected function alterFormForLva(Form $form)
    {

    }

    /**
     * No-op but extended
     */
    protected function alterFormForLocation(Form $form)
    {

    }

    /**
     * A method to be called post save, this can be hi-jacked to do things like update completion status
     */
    protected function postSave($section)
    {

    }

    /**
     * Reload the current page
     *
     * @return \Zend\Http\Response
     */
    protected function reload()
    {
        return $this->redirect()->toRoute(null, array(), array(), true);
    }

    /**
     * Delete file
     *
     * @NOTE This is public so it can be called as a callback when processing files
     *
     * @param int $id
     */
    public function deleteFile($id)
    {
        $documentService = $this->getServiceLocator()->get('Entity\Document');

        $identifier = $documentService->getIdentifier($id);

        if (!empty($identifier)) {
            $this->getServiceLocator()->get('FileUploader')->getUploader()->remove($identifier);
        }

        $documentService->delete($id);

        return true;
    }

    protected function processFiles($form, $selector, $uploadCallback, $deleteCallback, $loadCallback)
    {
        $uploadHelper = $this->getServiceLocator()->get('Helper\FileUpload');

        $uploadHelper->setForm($form)
            ->setSelector($selector)
            ->setUploadCallback($uploadCallback)
            ->setDeleteCallback($deleteCallback)
            ->setLoadCallback($loadCallback)
            ->setRequest($this->getRequest());

        return $uploadHelper->process();
    }

    /**
     * Upload a file
     *
     * @param array $fileData
     * @param array $data
     */
    protected function uploadFile($fileData, $data)
    {
        $uploader = $this->getServiceLocator()->get('FileUploader')->getUploader();
        $uploader->setFile($fileData);

        $file = $uploader->upload();

        $docData = array_merge(
            array(
                'filename'      => $file->getName(),
                'identifier'    => $file->getIdentifier(),
                'size'          => $file->getSize(),
                'fileExtension' => 'doc_' . $file->getExtension()
            ),
            $data
        );

        $this->getServiceLocator()->get('Entity\Document')->save($docData);
    }
}
