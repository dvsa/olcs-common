<?php
/**
 * Checks that if a withdrawn checkbox is ticked then the corresponding date is also filled in
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Form\Element\DateSelect as ZendDateSelect;

/**
 * Checks that if a withdrawn checkbox is ticked then the corresponding date is also filled in
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class WithdrawnDate extends ZendDateSelect implements InputProviderInterface
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
            'continue_if_empty' => true,
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
            'validators' => $this->getValidators()
        ];

        return $specification;
    }

    public function getValidators()
    {
        return array(
            new \Common\Form\Elements\Validators\WithdrawnDate()
        );
    }
}
