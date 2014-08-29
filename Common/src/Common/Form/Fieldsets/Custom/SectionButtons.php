<?php

/**
 * Section Buttons
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Fieldsets\Custom;

use Zend\Form\Fieldset;
use Common\Form\Elements\InputFilters\ActionButton;

/**
 * Section Buttons
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SectionButtons extends Fieldset
{
    public function __construct($name = '', $options = array())
    {
        parent::__construct('form-actions', $options);

        $this->setAttributes(array('class' => 'actions-container'));

        $submit = new ActionButton('save');
        $submit->setAttributes(
            array(
                'class' => 'action--primary large',
                'type' => 'submit'
            )
        );
        $submit->setLabel('Save');

        $this->add($submit);

        $cancel = new ActionButton('cancel');
        $cancel->setAttributes(
            array(
                'class' => 'action--secondary large',
                'type' => 'submit'
            )
        );
        $cancel->setLabel('Cancel');

        $this->add($cancel);
    }
}
