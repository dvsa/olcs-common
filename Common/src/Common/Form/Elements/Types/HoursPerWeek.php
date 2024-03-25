<?php

/**
 * Hours per week fieldset
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Types;

use Laminas\Form\Fieldset;

/**
 * Hours per week fieldset
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class HoursPerWeek extends Fieldset
{
    public function setMessages($messages): void
    {
        $this->messages = $messages;
    }

    public function getMessages(?string $elementName = null): array
    {
        return $this->messages;
    }
}
