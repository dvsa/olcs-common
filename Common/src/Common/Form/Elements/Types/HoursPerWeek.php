<?php

namespace Common\Form\Elements\Types;

use Laminas\Form\Fieldset;

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
