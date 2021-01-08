<?php

/**
 * Html Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\Elements\Types;

use Common\Form\Form;
use Laminas\Form\Element;

/**
 * Html Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Html extends Element
{
    /**
     * setValue
     *
     * @param mixed $value value
     *
     * @return void
     */
    public function setValue($value)
    {
        /**  #OLCS-17989 - overridden to ensure any injection cannot happen **/
        if (!Form::isPopulating()) {
            parent::setValue($value);
        }
    }
}
