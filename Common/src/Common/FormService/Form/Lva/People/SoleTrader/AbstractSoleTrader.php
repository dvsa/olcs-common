<?php

/**
 * Abstract Sole Trader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\People\SoleTrader;

use Common\FormService\Form\Lva\AbstractLvaFormService;

/**
 * Abstract Sole Trader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractSoleTrader extends AbstractLvaFormService
{
    public function getForm($params)
    {
        $form = $this->getFormHelper()->createForm('Lva\SoleTrader');

        $this->alterForm($form, $params);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form, array $params)
    {
        // if not internal OR no  person OR already disqualified then hide the disqualify button
        if ($params['location'] !== 'internal' || empty($params['personId']) || $params['isDisqualified']) {
            $this->removeFormAction($form, 'disqualify');
        } else {
            $form->get('form-actions')->get('disqualify')->setValue($params['disqualifyUrl']);
        }

        if (isset($params['canModify']) && $params['canModify'] === false) {
            $this->getServiceLocator()->get('Lva\People')->lockPersonForm($form, $params['orgType']);
        }

        return $form;
    }
}
