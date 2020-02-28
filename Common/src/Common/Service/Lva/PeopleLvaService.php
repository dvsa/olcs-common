<?php

/**
 * People LVA service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Lva;

use Common\RefData;
use Zend\Form\FieldsetInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Form\Form;

/**
 * People LVA service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PeopleLvaService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * lock person form
     *
     * @param Form  $form    form
     * @param mixed $orgType organisation type
     *
     * @return void
     */
    public function lockPersonForm(Form $form, $orgType)
    {
        /** @var FieldsetInterface $fieldset */
        $fieldset = $form->get('data');
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        foreach (['title', 'forename', 'familyName', 'otherName', 'birthDate', 'position'] as $field) {
            if ($fieldset->has($field)) {
                $formHelper->lockElement(
                    $fieldset->get($field),
                    'people.' . $orgType . '.' . $field . '.locked'
                );
                $formHelper->disableElement($form, 'data->' . $field);
            }
        }

        if ($orgType !== RefData::ORG_TYPE_SOLE_TRADER) {
            $formHelper->remove($form, 'form-actions->submit');
        }

        $form->setAttribute('locked', true);
    }

    /**
     * lock the partnership form
     *
     * @param Form  $form  form
     * @param mixed $table table
     *
     * @return void
     */
    public function lockPartnershipForm(Form $form, $table)
    {
        $table->removeActions();
        $table->removeColumn('select');
    }

    /**
     * lock the organisation form
     *
     * @param Form  $form  form
     * @param mixed $table table
     *
     * @return void
     */
    public function lockOrganisationForm(Form $form, $table)
    {
        $table->removeActions();
        $table->removeColumn('select');
        $table->removeColumn('actionLinks');
    }
}
