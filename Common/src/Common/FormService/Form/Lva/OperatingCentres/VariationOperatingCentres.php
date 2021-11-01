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

        if ($form->get('data')->has('totAuthHgvVehiclesFieldset')) {
            $hint = $translator->translateReplace('current-authorisation-hint', [$licence['totAuthHgvVehicles'] ?? 0]);
            $form->get('data')->get('totAuthHgvVehiclesFieldset')->get('totAuthHgvVehicles')->setOption('hint-below', $hint);
        }

        if ($form->get('data')->has('totAuthLgvVehiclesFieldset')) {
            $hint = $translator->translateReplace('current-authorisation-hint', [$licence['totAuthLgvVehicles'] ?? 0]);
            $form->get('data')->get('totAuthLgvVehiclesFieldset')->get('totAuthLgvVehicles')->setOption('hint-below', $hint);
        }

        if ($form->get('data')->has('totAuthTrailersFieldset')) {
            $hint = $translator->translateReplace('current-authorisation-hint', [$licence['totAuthTrailers']]);
            $form->get('data')->get('totAuthTrailersFieldset')->get('totAuthTrailers')->setOption('hint-below', $hint);
        }

        if ($form->get('data')->has('totCommunityLicencesFieldset')) {
            $this->getFormHelper()->disableElement($form, 'data->totCommunityLicencesFieldset->totCommunityLicences');
        }

        return $form;
    }
}
