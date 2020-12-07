<?php

/**
 * Inspection Request Due Date Validator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Laminas\Validator\AbstractValidator as AbstractValidator;

/**
 * Inspection Request Due Date Validator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InspectionRequestDueDate extends AbstractValidator
{
    /**
     * Error codes
     * @const string
     */
    const NOT_SAME_OR_MORE = 'notSameOrMore';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SAME_OR_MORE => "Due date should be the same or after date requested"
    );

    /**
     * Validate due date against requested date
     *
     * @param  mixed $value
     * @param  array $context
     * @return bool
     */
    public function isValid($value, array $context = null)
    {
        $dueDate = $value;
        $requestedDate = implode(
            '-',
            [$context['requestDate']['year'], $context['requestDate']['month'], $context['requestDate']['day']]
        );

        if (!($dueDate > $requestedDate)) {
            $this->error(self::NOT_SAME_OR_MORE);
            return false;
        }

        return true;
    }
}
