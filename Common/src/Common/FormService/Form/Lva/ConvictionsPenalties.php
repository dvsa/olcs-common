<?php

/**
 * Convictions and Penalties Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;
use Common\RefData;
use Common\Form\Elements\Types\Html;
use Dvsa\Olcs\Api\Entity\Licence;

/**
 * Convictions and Penalties Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ConvictionsPenalties extends AbstractFormService
{
    /**
     * get the form
     *
     * @param array $params parameters used for switching/configuring form
     *
     * @return \Common\Form\Form|\Laminas\Form\Form
     */
    public function getForm(array $params = [])
    {
        $form = $this->getFormHelper()->createForm('Lva\ConvictionsPenalties');
        $this->alterForm($form, $params);
        return $form;
    }

    /**
     * Determine if form changes required as is director variation
     *
     * @param array $params parameters used for switching/configuring form
     *
     * @return bool
     */
    public function isDirectorChange(array $params)
    {
        return isset($params['variationType']) &&
            $params['variationType'] === RefData::VARIATION_TYPE_DIRECTOR_CHANGE;
    }

    /**
     * Make form alterations
     *
     * @param \Laminas\Form\Form $form   form
     * @param array           $params parameters used for switching/configuring form
     *
     * @return \Laminas\Form\Form form
     *
     */
    protected function alterForm($form, array $params)
    {
        $this->changeFormForDirectorVariation($form, $params);
        $getConvictionsReadMoreLink = $form->get('convictionsReadMoreLink')->get('readMoreLink');
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $routeParam = $translator->translate('convictions-and-penalties-guidance-route-param');
        $getConvictionsReadMoreRoute = $this->getServiceLocator()->get('Helper\Url')->fromRoute(
            'guides/guide',
            ['guide' => $routeParam]
        );
        $getConvictionsReadMoreLink->setValue($getConvictionsReadMoreRoute);

        return $form;
    }

    /**
     * Alter the form standard buttons
     *
     * @param \Laminas\Form\Form $form form
     *
     * @return void
     *
     */
    private function alterFormButtons($form)
    {
        $formActions = $form->get('form-actions');
        $formActions->get('saveAndContinue')->setLabel('Submit details');
    }

    /**
     * Alter the confirmation message
     *
     * @param \Laminas\Form\Form $form form
     *
     * @return void
     */
    private function removeConfirmation($form)
    {
        $form->remove('convictionsConfirmation');
    }

    /**
     * Set dynamic label based on organisation type
     *
     * @param \Laminas\Form\Fieldset $dataTable fieldset for table
     * @param array               $params    parameters
     *
     * @return void
     */
    private function alterFormHeading($dataTable, array $params)
    {
        $label = $dataTable->getLabel();
        $dataTable->setLabel($label . '-' . $params['organisationType'] . "-dc");
        $existingClasses = $dataTable->getAttribute('class');
        $dataTable->setAttribute('class', $existingClasses . ' five-eights');
    }

    /**
     * remove form question text
     *
     * @param \Laminas\Form\Fieldset $dataTable data table
     *
     * @return void
     */
    private function alterFormQuestion($dataTable)
    {
        $question = $dataTable->get('question');
        $question->setLabel('');
    }

    /**
     * Change the form for director variation
     *
     * @param \Laminas\Form\Form $form   form
     * @param array           $params params
     *
     * @return void
     */
    public function changeFormForDirectorVariation($form, array $params)
    {
        if ($this->isDirectorChange($params)) {
            $dataTable = $form->get('data');
            $existingClasses = $dataTable->getAttribute('class');
            $dataTable->setAttribute('class', $existingClasses . ' director-change');
            $this->alterFormQuestion($dataTable);
            $this->alterFormHeading($dataTable, $params);
            $this->alterFormButtons($form);
            $this->removeConfirmation($form);
            $this->getFormHelper()->remove($form, 'form-actions->save');
        }
    }
}
