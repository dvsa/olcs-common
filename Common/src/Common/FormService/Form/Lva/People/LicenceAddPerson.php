<?php

namespace Common\FormService\Form\Lva\People;

use Common\Form\Elements\Types\Html;
use Common\Form\Form;
use Common\Form\Model\Form\Licence\AddPerson;
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

        $this->addFieldsetHeading($form, $params['organisationType']);
        $this->addRemoveLink($form);
        $this->addClass($form);
        $this->changeButtonForOrganisation($form, $params['organisationType']);
        return $form;
    }

    /**
     * Add remove link
     *
     * @param Form $form Form
     *
     * @return void
     */
    private function addRemoveLink(Form $form)
    {
        $targetElement = $this->getTargetElementInCollection($form);

        $translator = $this->getTranslator();

        $removeLink = new Html('removeLink');
        $removeLink->setValue('<a href="#">' . $translator->translate('Remove this') . '</a>');
        $removeLink->setAttribute('data-container-class', 'remove-link');

        $targetElement->add($removeLink, ['priority' => 1]);
    }

    /**
     * Alters the form for add another
     *
     * @param Form   $form             form
     * @param string $organisationType organisationHeading dynamic heading for organisation
     *
     * @return void
     */
    protected function addFieldsetHeading(Form $form, $organisationType)
    {
        $targetElement = $this->getTargetElementInCollection($form);

        $headingText = $this->getOrganisationHeading($organisationType);

        $heading = new Html('heading');
        $heading->setValue($headingText);
        $targetElement->add($heading, ['priority' => 2]);
    }

    /**
     * getTargetElementInCollection
     *
     * @param Form $form Form
     *
     * @return FieldsetInterface
     */
    private function getTargetElementInCollection(Form $form)
    {
        $fieldset = $this->getDataFieldset($form);

        return $fieldset->getTargetElement();
    }

    /**
     * getDataFieldset
     *
     * @param Form $form Form
     *
     * @return Collection
     */
    private function getDataFieldset(Form $form)
    {
        return $form->getFieldsets()['data'];
    }

    /**
     * addClass
     *
     * @param Form $form Form
     *
     * @return void
     */
    private function addClass(Form $form)
    {
        $dataFieldset = $this->getDataFieldset($form);

        $existingClasses = $dataFieldset->getAttribute('class');
        $dataFieldset->setAttribute('class', $existingClasses . ' add-another-director-change');
    }

    /**
     * Get the organisation specific heading
     *
     * @param string $organisationType Org type
     *
     * @return string
     */
    protected function getOrganisationHeading($organisationType)
    {
        $translator = $this->getTranslator();
        $headingText = 'licence_add-Person-PersonType-';
        $headingText = sprintf('<h2>%s</h2>', $translator->translate($headingText . $organisationType));
        return $headingText;
    }

    /**
     * Get the translator
     *
     * @return array|object
     */
    private function getTranslator()
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        return $translator;
    }

    /**
     * Change the b utton text based on organisation
     *
     * @param Form   $form             form
     * @param string $organisationType organisation type [org_t_rc etc]
     *
     * @return void
     */
    private function changeButtonForOrganisation(Form $form, $organisationType)
    {
        $fieldset = $this->getDataFieldset($form);
        $fieldset->setOption('hint', 'markup-add-another-director-hint-' . $organisationType);
    }
}
