<?php

namespace Common\FormService\Form\Lva\People;

use Common\Form\Elements\Types\Html;
use Common\Form\Form;
use Common\Form\Model\Form\Licence\AddPerson;
use Laminas\Form\Element\Collection;
use Laminas\Form\FieldsetInterface;

/**
 * Licence People
 */
class LicenceAddPerson extends AbstractPeople
{
    /**
     * Get the form
     *
     * @param array $params params
     *
     * @return Form $form form
     */
    public function getForm(array $params = [])
    {
        return $this->getFormHelper()->createForm(AddPerson::class);
    }
}
