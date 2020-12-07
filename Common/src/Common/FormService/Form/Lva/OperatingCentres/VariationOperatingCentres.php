<?php

/**
 * Variation Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\OperatingCentres;

use Laminas\Form\Form;

/**
 * Variation Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentres extends AbstractOperatingCentres
{
    protected $mainTableConfigName = 'lva-variation-operating-centres';

    protected function alterForm(Form $form, array $params)
    {
        $this->getFormServiceLocator()->get('lva-variation')->alterForm($form);

        parent::alterForm($form, $params);

        $translator = $this->getServiceLocator()->get('Helper\Translation');

        $licence = $params['licence'];

        if ($form->get('data')->has('totAuthVehicles')) {
            $hint = $translator->translateReplace('current-authorisation-hint', [$licence['totAuthVehicles']]);
            $form->get('data')->get('totAuthVehicles')->setOption('hint', $hint);
        }

        if ($form->get('data')->has('totAuthTrailers')) {
            $hint = $translator->translateReplace('current-authorisation-hint', [$licence['totAuthTrailers']]);
            $form->get('data')->get('totAuthTrailers')->setOption('hint', $hint);
        }

        if ($form->get('data')->has('totCommunityLicences')) {
            $this->getFormHelper()->disableElement($form, 'data->totCommunityLicences');
        }

        return $form;
    }
}
