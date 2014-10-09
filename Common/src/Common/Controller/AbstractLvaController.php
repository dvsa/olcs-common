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
    use Util\HelperServiceAware,
        Util\EntityServiceAware,
        Util\FlashMessengerTrait;

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
    protected function getAccessibleSections()
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

        $sections = $this->getHelperService('AccessHelper')->setSections($inputSections)
            ->getAccessibleSections($access);

        return array_keys($sections);
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
}
