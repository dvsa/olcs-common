<?php

/**
 * AbstractLvaController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller;

use Common\Util;
use Common\Service\Data\SectionConfig;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;

/**
 * AbstractLvaController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLvaController extends AbstractActionController
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
            $actionResponse = $this->$method();
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
}
