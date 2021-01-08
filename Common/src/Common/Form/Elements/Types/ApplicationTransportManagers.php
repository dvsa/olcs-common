<?php

/**
 * ApplicationTransportManagers fieldset
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Common\Form\Elements\Types;

use Laminas\Form\Fieldset;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Select;
use Common\Form\Elements\Types\HtmlTranslated;

/**
 * ApplicationTransportManagers fieldset
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class ApplicationTransportManagers extends Fieldset
{

    /**
     * Setup the elements
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $application = new Text('application');
        $application->setAttributes(
            array(
                'class' => 'short js-input',
                'data-container-class' => 'inline'
            )
        );
        $application->setOption('remove_if_readonly', true);

        $this->add($application);

        $searchButton = new Button('search', array('label' => 'Find application'));
        $searchButton->setAttributes(
            array(
                'type' => 'submit',
                'class' => 'action--primary large js-find',
                'data-container-class' => 'inline'
            )
        );
        $searchButton->setValue('search');

        $this->add($searchButton);
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages($elementName = null)
    {
        return $this->messages;
    }
}
