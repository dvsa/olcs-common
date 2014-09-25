<?php

/**
 * Abstract Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Util\RestCallTrait;

/**
 * Abstract Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractSectionService implements SectionServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait,
        RestCallTrait;

    /**
     * Holds the service
     *
     * @var string
     */
    protected $service;

    /**
     * Cache the translator
     *
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * Cache the factory instance
     *
     * @var \Common\Controller\Service\SectionServiceFactory
     */
    private $sectionServiceFactory;

    /**
     * Cache the section services
     *
     * @var array
     */
    private $sectionServices = array();

    /**
     * Hold the identifier
     *
     * @var int
     */
    private $identifier;

    /**
     * Holds whether the section is an action
     *
     * @var boolean
     */
    private $isAction;

    /**
     * Holds the actionId
     *
     * @var int
     */
    private $actionId;

    /**
     * Holds the action name
     *
     * @var string
     */
    private $actionName;

    /**
     * Holds the request
     *
     * @var \Zend\Http\Request
     */
    private $request;

    /**
     *
     * @param \Common\Controller\Service\SectionServiceFactory $factory
     */
    public function setSectionServiceFactory(SectionServiceFactory $factory)
    {
        $this->sectionServiceFactory = $factory;
    }

    /**
     * Get another section service
     *
     * @param string $name
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    protected function getSectionService($name)
    {
        if (!isset($this->sectionServices[$name])) {
            $this->sectionServices[$name] = $this->sectionServiceFactory->getSectionService($name);
            $this->sectionServices[$name]->setIdentifier($this->getIdentifier());
            $this->sectionServices[$name]->setIsAction($this->isAction());
            $this->sectionServices[$name]->setActionId($this->getActionId());
            $this->sectionServices[$name]->setActionName($this->getActionName());
            $this->sectionServices[$name]->setRequest($this->getRequest());
        }

        return $this->sectionServices[$name];
    }

    /**
     * Lock the element
     *
     * @param \Zend\Form\Element $element
     * @param string $message
     */
    public function lockElement(Element $element, $message)
    {
        $viewRenderer = $this->getServiceLocator()->get('ViewRenderer');

        $lockView = new ViewModel(array('message' => $this->translate($message)));
        $lockView->setTemplate('partials/lock');

        $element->setLabel($element->getLabel() . $viewRenderer->render($lockView));
        $element->setLabelOption('disable_html_escape', true);

        $attributes = $element->getLabelAttributes();

        if (!isset($attributes['class'])) {
            $attributes['class'] = '';
        }
        // @todo add this back in when the css has been tweaked
        //$attributes['class'] .= ' tooltip-grandparent';

        $element->setLabelAttributes($attributes);
    }

    /**
     * Format a translation string
     *
     * @param type $format
     * @param type $messages
     * @return type
     */
    public function formatTranslation($format, $messages)
    {
        if (!is_array($messages)) {
            return $this->wrapTranslation($format, $messages);
        }

        array_walk(
            $messages,
            function (&$value) {
                $value = $this->translate($value);
            }
        );

        return vsprintf($format, $messages);
    }

    /**
     * Wrap a translated message with the wrapper
     *
     * @param string $wrapper
     * @param string $message
     * @return string
     */
    public function wrapTranslation($wrapper, $message)
    {
        return sprintf($wrapper, $this->translate($message));
    }

    /**
     * Translate a message
     *
     * @param string $message
     * @return string
     */
    public function translate($message)
    {
        return $this->getTranslator()->translate($message);
    }

    /**
     * Get translator
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if ($this->translator === null) {
            $this->translator = $this->getServiceLocator()->get('translator');
        }

        return $this->translator;
    }

    /**
     * Remove a list of form fields
     *
     * @param \Zend\Form\Form $form
     * @param string $fieldset
     * @param array $fields
     */
    public function removeFormFields(Form $form, $fieldset, array $fields)
    {
        foreach ($fields as $field) {
            $form->get($fieldset)->remove($field);
        }
    }

    /**
     * Set the identifier
     *
     * @param int $id
     */
    public function setIdentifier($id)
    {
        $this->identifier = $id;
    }

    /**
     * Getter for identifier
     *
     * @return int
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Setter for isAction
     *
     * @param boolean $isAction
     */
    public function setIsAction($isAction)
    {
        $this->isAction = $isAction;
    }

    /**
     * Getter for isAction
     *
     * @return boolean
     */
    public function IsAction()
    {
        return $this->isAction;
    }

    /**
     * Setter for actionId
     *
     * @param int $actionId
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;
    }

    /**
     * Getter for actionId
     *
     * @return int
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * Setter for actionName
     *
     * @param string $actionName
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * Get action name
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * Setter for request
     *
     * @param \Zend\Http\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Getter for request
     *
     * @return \Zend\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Save sub action data
     *
     * @param array $data
     */
    protected function actionSave($data, $service = null)
    {
        if (is_null($service)) {
            $service = $this->getActionService();
        }

        return $this->save($data, $service);
    }

    /**
     * Save the data
     *
     * @param array $data
     */
    public function save($data, $service = null)
    {
        $method = 'POST';

        if (isset($data['id']) && !empty($data['id'])) {
            $method = 'PUT';
        }

        if (empty($service)) {
            $service = $this->getService();
        }

        return $this->makeRestCall($service, $method, $data);
    }
}
