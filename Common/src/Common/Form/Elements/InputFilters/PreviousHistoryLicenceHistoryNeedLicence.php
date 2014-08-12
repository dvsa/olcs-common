<?php

/**
 * PreviousHistoryLicenceHistoryNeedLicence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\PreviousHistoryLicenceHistoryLicenceValidator;
use Zend\Form\Element\Radio as ZendRadio;

/**
 * PreviousHistoryLicenceHistoryNeedLicence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PreviousHistoryLicenceHistoryNeedLicence extends ZendRadio implements InputProviderInterface
{
    protected $continueIfEmpty = true;
    protected $allowEmpty = false;

    /**
     * Get a list of validators
     *
     * @return array
     */
    protected function getValidators()
    {
        return array(
            new PreviousHistoryLicenceHistoryLicenceValidator()
        );
    }
}
