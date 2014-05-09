<?php
/**
 *
 * @author Jakub.Igla <jakub.igla@valtech.co.uk
 *
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\DateSelect as ZendDateSelect;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator\Date as DateValidator;

/**
 * Checks conviction offence date is before the conviction date
 */
class OffenceDateBeforeConvictionDate extends ZendDateSelect implements InputProviderInterface
{
    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $specification = [
            'name' => $this->getName(),
            'required' => false,
            'filters' => array(
                array(
                    'name'    => 'Callback',
                    'options' => array(
                        'callback' => function ($date) {
                        // Convert the date to a specific format
                            if (!is_array($date) || empty($date['year']) ||
                                empty($date['month']) || empty($date['day'])) {
                                return null;
                            }

                            return $date['year'] . '-' . $date['month'] . '-' . $date['day'];
                        }
                    )
                )
            ),
            'validators' => [
                new \Common\Form\Elements\Validators\DateNotInFuture(),
                new \Common\Form\Elements\Validators\DateLessThanOrEqual('dateOfConviction'),
                new DateValidator(array('format' => 'Y-m-d'))
            ]
        ];

        return $specification;
    }
}
