<?php

/**
 * PreviousHistoryPenaltiesConvictionsPrevConviction
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\PreviousHistoryPenaltiesConvictionsPrevConvictionValidator;
use Zend\Form\Element\Radio as ZendRadio;

/**
 * PreviousHistoryPenaltiesConvictionsHasOffence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PreviousHistoryPenaltiesConvictionsPrevConviction extends ZendRadio implements InputProviderInterface
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
            new PreviousHistoryPenaltiesConvictionsPrevConvictionValidator()
        );
    }
}
