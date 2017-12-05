<?php

namespace Common\FormService\Form\Lva\People;

use Common\Form\Elements\Custom\DateSelect;
use Common\Form\Elements\Types\Html;
use Common\Form\Form;
use Common\Form\Model\Form\Licence\AddPerson;
use Zend\Form\Element;
use Zend\Form\Element\Collection;
use Zend\Form\FieldsetInterface;

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
        $form = $this->getFormHelper()->createForm(AddPerson::class);
        $this->alterForm($form, $params);

        return $form;
    }

    /**
     * Alter licence add person form
     *
     * @param Form  $form   Form
     * @param array $params Parameters / options for form
     *
     * @return Form
     */
    protected function alterForm(Form $form, array $params = [])
    {
        $form = parent::alterForm($form, $params);
        $this->addRemoveLink($form);
        $this->alterFormForAddAnother($form, "stuff");


        return $form;
    }

    /**
     * Add remove link
     *
     * @param Form $form
     */
    private function addRemoveLink(Form $form)
    {
        /** @var Collection $fieldset */
        $fieldset = $form->getFieldsets()['data'];

        $translator = $this->getServiceLocator()->get('Helper\Translation');

        $removeLink = new Html('removeLink');
        $removeLink->setValue('<a href="#">' . $translator->translate('Remove this') . '</a>');
        $removeLink->setAttribute('data-container-class', 'remove-link');

        /** @var FieldsetInterface $targetElement */
        $targetElement = $fieldset->getTargetElement();
        $targetElement->add($removeLink, ['priority' => 1, 'name' => 'aoue']);
    }

    /**
     * Alters the form for add another
     *
     * @param Form $form form
     * @param string organisationHeading dynamic heading for organisation
     *
     * @return void
     */
    protected function alterFormForAddAnother(Form $form, $organisationType)
    {
        /** @var Collection $fieldset */
        $dataFieldset = $form->getFieldsets()['data'];
        $existingClasses = $dataFieldset->getAttribute('class');
        $dataFieldset->setAttribute('class', $existingClasses . ' add-another-director-change');
        $targetElement = $dataFieldset->getTargetElement();
        $heading = new Html('heading');
        $headingText = sprintf('<h2>%s</h2>', $organisationType);
        $heading->setValue($headingText);
        $targetElement->add($heading, ['priority' => 2]);
    }
}
